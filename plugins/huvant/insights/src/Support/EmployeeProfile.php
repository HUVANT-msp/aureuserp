<?php

namespace Huvant\Insights\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Contracts\Auth\Authenticatable;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\ProfileExtension;

/**
 * People keep their own employee record from their profile: the work details
 * below, and their name and e-mail, go straight to the employee.
 */
class EmployeeProfile implements ProfileExtension
{
    /** Profile field => employee column. */
    public const FIELDS = [
        'job_title'         => 'job_title',
        'work_phone'        => 'work_phone',
        'mobile_phone'      => 'mobile_phone',
        'private_phone'     => 'private_phone',
        'private_email'     => 'private_email',
        'emergency_contact' => 'emergency_contact',
        'emergency_phone'   => 'emergency_phone',
        'birthday'          => 'birthday',
    ];

    public static function employeeOf(Authenticatable $user): ?Employee
    {
        // The person's own record, whatever company or ownership scope the viewer has.
        return Employee::withoutGlobalScopes()->where('user_id', $user->getAuthIdentifier())->whereNull('deleted_at')->first();
    }

    public static function components(Authenticatable $user): array
    {
        if (! static::employeeOf($user)) {
            return [];
        }

        return [
            Section::make('Work details')
                ->description('Your employee record: what you save here is what the team and the administrators see.')
                ->icon('heroicon-o-identification')
                ->statePath('employee')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('job_title')->label('Job title')->maxLength(255),
                        DatePicker::make('birthday')->label('Birthday')->native(false)->maxDate(now()),
                        TextInput::make('work_phone')->label('Work phone')->tel()->maxLength(255),
                        TextInput::make('mobile_phone')->label('Mobile phone')->tel()->maxLength(255),
                        TextInput::make('private_phone')->label('Private phone')->tel()->maxLength(255),
                        TextInput::make('private_email')->label('Private e-mail')->email()->maxLength(255),
                        TextInput::make('emergency_contact')->label('Emergency contact')->maxLength(255),
                        TextInput::make('emergency_phone')->label('Emergency phone')->tel()->maxLength(255),
                    ]),
                ]),
        ];
    }

    public static function fill(Authenticatable $user): array
    {
        $employee = static::employeeOf($user);
        if (! $employee) {
            return [];
        }

        return ['employee' => collect(self::FIELDS)->mapWithKeys(fn (string $column, string $field): array => [
            $field => $column === 'birthday' && $employee->birthday ? substr((string) $employee->getRawOriginal('birthday'), 0, 10) : $employee->{$column},
        ])->all()];
    }

    public static function save(Authenticatable $user, array $data): void
    {
        $employee = static::employeeOf($user);
        if (! $employee) {
            return;
        }
        $values = [];
        foreach (self::FIELDS as $field => $column) {
            if (array_key_exists($field, $data['employee'] ?? [])) {
                $value = $data['employee'][$field];
                $values[$column] = is_string($value) ? (trim($value) === '' ? null : trim($value)) : $value;
            }
        }
        // Quietly: the employee's own "saved" hook looks its contact up through the
        // viewer's visibility scopes and, when a person cannot see it, creates a new
        // one and saves again, forever. The contact card is kept in step here instead.
        $employee->forceFill($values)->saveQuietly();
        static::syncContact($employee);
        static::syncIdentity($user);
    }

    /** Name and work e-mail follow the user, wherever the user is changed. */
    public static function syncIdentity(Authenticatable $user): void
    {

        if (! $user instanceof User) {
            return;
        }
        Employee::withoutGlobalScopes()->where('user_id', $user->getKey())->whereNull('deleted_at')->get()->each(function (Employee $employee) use ($user): void {
            $employee->forceFill(['name' => $user->name, 'work_email' => $user->email])->saveQuietly();
            static::syncContact($employee);
        });
    }

    /** The employee's contact card shows the same name, e-mail, job title and phones. */
    private static function syncContact(Employee $employee): void
    {
        if (! $employee->partner_id) {
            return;
        }
        Partner::withoutGlobalScopes()->whereKey($employee->partner_id)->first()?->forceFill([
            'name'      => $employee->name,
            'email'     => $employee->work_email ?? $employee->private_email,
            'job_title' => $employee->job_title,
            'phone'     => $employee->work_phone,
            'mobile'    => $employee->mobile_phone,
        ])->saveQuietly();
    }
}
