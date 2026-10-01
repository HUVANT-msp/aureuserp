<?php

namespace Huvant\Insights\Support;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Huvant\Bridge\Support\MinutesApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

/**
 * "New employee": the one place to add a colleague. It creates their user, so
 * they are an employee and an internal contact at once (Onboarding), and sends
 * them the invitation to set a password.
 */
class NewEmployee
{
    public const ROLES = ['team' => 'Team Huvant', 'admin' => 'Admin'];

    public static function action(): Action
    {
        return Action::make('newEmployee')
            ->label('New employee')
            ->icon('heroicon-o-user-plus')
            ->modalHeading('New employee')
            ->modalDescription(fn (): string => static::invitesOn()
                ? 'They get an account, appear among employees and internal contacts, and receive an e-mail to set their password.'
                : 'They get an account and appear among employees and internal contacts. No e-mail is sent for now.')
            ->modalSubmitActionLabel(fn (): string => static::invitesOn() ? 'Add and invite' : 'Add')
            ->modalWidth('lg')
            ->schema([
                TextInput::make('name')->label('Full name')->required()->maxLength(255)->autofocus(),
                TextInput::make('email')->label('Work e-mail')->email()->required()->maxLength(255)
                    ->unique(table: 'users', column: 'email'),
                Grid::make(2)->schema([
                    TextInput::make('job_title')->label('Job title')->maxLength(255),
                    Select::make('parent_id')->label('Manager')
                        ->options(fn () => Employee::query()->whereNotNull('user_id')->orderBy('name')->pluck('name', 'id'))
                        ->searchable(),
                ]),
                ToggleButtons::make('access')->label('Access')->inline()->required()->default('team')
                    ->options(['team' => 'Team member', 'admin' => 'Administrator'])
                    ->icons(['team' => 'heroicon-m-user', 'admin' => 'heroicon-m-shield-check']),
            ])
            ->action(function (array $data): void {
                $employee = static::create($data, auth()->user());
                if (static::invitesOn()) {
                    static::invite($employee->user, auth()->user());
                } else {
                    Notification::make()->success()->title($employee->name.' added')
                        ->body('They are among employees and internal contacts. Invitations are off for now: no e-mail was sent.')->send();
                }
            });
    }

    /** The user (and so the employee and the contact), with the access chosen. */
    public static function create(array $data, ?User $by = null): Employee
    {
        return DB::transaction(function () use ($data, $by): Employee {
            $companyId = $by?->default_company_id ?? DB::table('companies')->orderBy('id')->value('id');
            $user = User::query()->create([
                'name'                => trim($data['name']),
                'email'               => Str::lower(trim($data['email'])),
                // Unknown to anyone: they choose their own from the invitation.
                'password'            => Hash::make(Str::password(40)),
                'is_active'           => true,
                'default_company_id'  => $companyId,
                'resource_permission' => PermissionType::GLOBAL,
            ]);
            $role = Role::query()->where('name', self::ROLES[$data['access'] ?? 'team'] ?? self::ROLES['team'])->first();
            if ($role) {
                $user->syncRoles([$role]);
            }
            if ($companyId) {
                $user->allowedCompanies()->syncWithoutDetaching([$companyId]);
            }

            $employee = Onboarding::onboard($user->refresh());
            $employee->forceFill(array_filter([
                'job_title' => filled($data['job_title'] ?? null) ? trim($data['job_title']) : null,
                'parent_id' => $data['parent_id'] ?? null,
            ], fn ($v) => $v !== null))->saveQuietly();
            EmployeeProfile::syncIdentity($user);

            return $employee->refresh();
        });
    }

    /** Invitations go out only once the ERP is in use (config huvant-insights.send_invites). */
    public static function invitesOn(): bool
    {
        return (bool) config('huvant-insights.send_invites', false);
    }

    /** E-mail the "set your password" link (sent by Meetings); if it cannot go, hand the link to the admin. */
    public static function invite(User $user, ?User $by = null, bool $notify = true): bool
    {
        $token = Password::broker(Filament::getPanel('admin')->getAuthPasswordBroker())->createToken($user);
        $url = Filament::getPanel('admin')->getResetPasswordUrl($token, $user);
        try {
            MinutesApi::post('welcome-email', ['email' => $user->email, 'name' => $user->name, 'url' => $url, 'invited_by' => $by?->name], 20);
            if (! $notify) {
                return true;
            }
            Notification::make()->success()->title($user->name.' added')
                ->body('They are among employees and internal contacts, and got an e-mail to set their password.')->send();

            return true;
        } catch (\Throwable $e) {
            report($e);
            if (! $notify) {
                return false;
            }
            Notification::make()->warning()->persistent()->title($user->name.' added, but the e-mail could not be sent')
                ->body('Send them this link to set their password (valid 3 days): '.$url)
                ->actions([NotificationAction::make('open')->label('Open link')->url($url, shouldOpenInNewTab: true)])
                ->send();

            return false;
        }
    }
}
