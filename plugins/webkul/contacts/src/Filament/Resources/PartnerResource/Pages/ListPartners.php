<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Contact\Filament\Resources\PartnerResource;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListPartners;
use Webkul\Partner\Models\Partner;
use Webkul\TableViews\Filament\Components\PresetView;

/** Contacts are organised in three lists only: the team, external people, and companies. */
class ListPartners extends BaseListPartners
{
    protected static string $resource = PartnerResource::class;

    public const INTERNAL_TAG = 'Team Huvant';

    public const EXTERNAL_TAG = 'Esterno';

    protected function getHeaderActions(): array
    {
        return [$this->broadcastAction(), ...parent::getHeaderActions()];
    }

    /** Ticks colleagues of the team and gives their addresses as one line, ready to paste in an email. */
    protected function broadcastAction(): Action
    {
        $isIt = app()->getLocale() === 'it';

        return Action::make('broadcast')
            ->label($isIt ? 'Nuovo broadcast' : 'New broadcast')
            ->icon('heroicon-o-megaphone')
            ->color('gray')
            ->modalHeading($isIt ? 'Nuovo broadcast' : 'New broadcast')
            ->modalDescription($isIt
                ? 'Scegli i colleghi: i loro indirizzi compaiono qui sotto, pronti da copiare nei destinatari.'
                : 'Pick colleagues: their addresses show below, ready to copy into the recipients.')
            ->modalWidth('2xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel($isIt ? 'Chiudi' : 'Close')
            ->schema([
                CheckboxList::make('recipients')
                    ->label($isIt ? 'Contatti interni' : 'Internal contacts')
                    ->options(fn (): array => static::internalEmails()->all())
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(2)
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?array $state) => $set('emails', static::emailLine($state ?? []))),
                TextInput::make('emails')
                    ->label($isIt ? 'Indirizzi' : 'Addresses')
                    ->placeholder($isIt ? 'Seleziona almeno un contatto' : 'Pick at least one contact')
                    ->readOnly()
                    ->copyable(copyMessage: $isIt ? 'Copiato' : 'Copied')
                    ->helperText(fn (Get $get): string => ($count = count($get('recipients') ?? [])) === 1
                        ? ($isIt ? '1 indirizzo' : '1 address')
                        : ($isIt ? "{$count} indirizzi" : "{$count} addresses")),
            ]);
    }

    /** @return Collection<int, string> partner id => "Name <email>", the team members with an email */
    public static function internalEmails(): Collection
    {
        return Partner::query()
            ->where('account_type', AccountType::INDIVIDUAL)
            ->whereHas('tags', fn (Builder $query) => $query->where('name', self::INTERNAL_TAG))
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->mapWithKeys(fn (Partner $partner): array => [$partner->id => "{$partner->name} <{$partner->email}>"]);
    }

    /** @param  array<int, int|string>  $partnerIds */
    public static function emailLine(array $partnerIds): string
    {
        return Partner::query()
            ->whereKey($partnerIds)
            ->orderBy('name')
            ->pluck('email')
            ->map(fn (string $email): string => trim($email))
            ->filter()
            ->unique()
            ->implode(', ');
    }

    public function getPresetTableViews(): array
    {
        $tagged = fn (string $tag) => fn (Builder $query) => $query->whereHas('tags', fn (Builder $q) => $q->where('name', $tag));

        return [
            'internal' => PresetView::make('Internal')
                ->icon('heroicon-s-user-group')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $tagged(self::INTERNAL_TAG)($query->where('account_type', AccountType::INDIVIDUAL))),

            'external' => PresetView::make('External')
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $tagged(self::EXTERNAL_TAG)($query->where('account_type', AccountType::INDIVIDUAL))),

            'companies' => PresetView::make('Companies')
                ->icon('heroicon-s-building-office')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::COMPANY)),
        ];
    }

    public function hasDefaultTableView(): bool
    {
        return false;
    }

    public function hasTableViewsMenu(): bool
    {
        return false;
    }
}
