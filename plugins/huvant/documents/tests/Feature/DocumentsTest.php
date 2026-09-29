<?php

use Filament\Facades\Filament;
use Huvant\Documents\Filament\Pages\ManageProjectDocuments;
use Huvant\Documents\Filament\Pages\ManageTaskDocuments;
use Huvant\Documents\Models\Document;
use Huvant\Documents\Models\Folder;
use Huvant\Documents\Support\Documents;
use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-teams');
    TestBootstrapHelper::ensurePluginInstalled('huvant-documents');
    ProjectTeams::forget();
    Storage::fake(Documents::DISK);
});

function docUser(?Team $team = null): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
    $team?->users()->attach($user->getKey());

    return $user;
}

function docProject(?Team $team): Project
{
    $project = Project::withoutEvents(fn (): Project => Project::factory()->create());
    if ($team) {
        ProjectTeams::syncProjectTeams($project->getKey(), [$team->getKey()]);
    }

    return $project;
}

// Upstream TaskFactory writes a non-existent "visibility" column: insert directly.
function docTask(Project $project): int
{
    return DB::table('projects_tasks')->insertGetId([
        'title'      => 'Task '.uniqid(), 'state' => 'in_progress', 'project_id' => $project->getKey(),
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

function docAdmin(): User
{
    return User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(docUser())->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
}

it('shows a project file only to the project teams and serves it privately', function () {
    $team = Team::query()->create(['name' => 'Atlas']);
    $member = docUser($team);
    $outsider = docUser(Team::query()->create(['name' => 'Altri']));
    $project = docProject($team);

    $document = Documents::storeUpload($member, $project->getKey(), null, null, UploadedFile::fake()->create('offerta.pdf', 120, 'application/pdf'));

    expect($document->title)->toBe('offerta.pdf')
        ->and($document->previewable())->toBeTrue();
    Storage::disk(Documents::DISK)->assertExists($document->path);

    $this->actingAs($member)->get(route('huvant.documents.show', $document))->assertOk()->assertDownload('offerta.pdf');
    ProjectTeams::forget();
    $this->actingAs($outsider)->get(route('huvant.documents.show', $document))->assertNotFound();
});

it('keeps task documents with the task and in the project', function () {
    $team = Team::query()->create(['name' => 'Atlas']);
    $member = docUser($team);
    $project = docProject($team);
    $task = docTask($project);

    $note = Documents::createNote($member, $project->getKey(), null, $task, 'Verbale tecnico', '<p>Ok</p>');
    $this->actingAs($member);

    expect(Documents::canView($note))->toBeTrue()
        ->and(Documents::counts($project->getKey()))->toBe(['documents' => 0, 'task' => 1]);
});

it('manages folders: unique names, only empty ones can go', function () {
    $team = Team::query()->create(['name' => 'Atlas']);
    $member = docUser($team);
    $project = docProject($team);

    $contracts = Documents::createFolder($member, $project->getKey(), null, 'Contratti');
    $signed = Documents::createFolder($member, $project->getKey(), $contracts->getKey(), 'Firmati');

    expect(fn () => Documents::createFolder($member, $project->getKey(), null, 'contratti'))->toThrow(RuntimeException::class)
        ->and(Documents::folderOptions($project->getKey()))->toBe([$contracts->getKey() => 'Contratti', $signed->getKey() => 'Contratti / Firmati'])
        ->and($signed->trail())->toHaveCount(2);

    $file = Documents::storeUpload($member, $project->getKey(), $signed->getKey(), null, UploadedFile::fake()->create('nda.docx', 10));
    expect(fn () => Documents::deleteFolder($signed))->toThrow(RuntimeException::class);

    $path = $file->path;
    $file->delete();
    Storage::disk(Documents::DISK)->assertMissing($path);
    Documents::deleteFolder($signed->fresh());
    expect(Folder::query()->whereKey($signed->getKey())->exists())->toBeFalse();
});

it('lets authors and administrators delete, anyone in the team edit notes', function () {
    $team = Team::query()->create(['name' => 'Atlas']);
    [$author, $colleague] = [docUser($team), docUser($team)];
    $project = docProject($team);
    $note = Documents::createNote($author, $project->getKey(), null, null, 'Idee', null);

    expect(Documents::canManage($author, $note))->toBeTrue()
        ->and(Documents::canManage($colleague, $note))->toBeFalse()
        ->and(Documents::canManage(docAdmin(), $note))->toBeTrue();
});

it('renders the project and task documents tabs', function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $admin = docAdmin();
    $project = docProject(null);
    $task = docTask($project);
    $this->actingAs($admin);

    $page = Livewire::test(ManageProjectDocuments::class, ['record' => $project->getKey()])->assertOk()
        ->callAction('newFolder', ['name' => 'Contratti'])
        ->assertHasNoActionErrors()
        ->assertSee('Contratti')
        ->callAction('newNote', ['title' => 'Kick-off', 'body' => '<p>Note</p>'])
        ->assertSee('Kick-off');

    $folder = Folder::query()->where('project_id', $project->getKey())->firstOrFail();
    $page->call('openFolder', (string) $folder->getKey())->assertSee('Nessun documento')->assertDontSee('Kick-off');

    Livewire::test(ManageTaskDocuments::class, ['record' => $task])->assertOk()
        ->callAction('newNote', ['title' => 'Checklist', 'body' => null])
        ->assertSee('Checklist');

    expect(Document::query()->where('task_id', $task)->value('project_id'))->toBe($project->getKey());
    $this->get(ManageProjectDocuments::getUrl(['record' => $project->getKey()]))->assertOk()->assertSee('Allegati ai task');
});

it('refuses downloads without a session', function () {
    $project = docProject(null);
    $document = Documents::storeUpload(docAdmin(), $project->getKey(), null, null, UploadedFile::fake()->create('a.txt', 1));

    $this->get(route('huvant.documents.show', $document))->assertForbidden();
});
