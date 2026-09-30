<?php

namespace Huvant\Bridge\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Models\Tag;

/**
 * External people named in meetings, once structured in the Minutes (first and
 * last name, company, e-mail): an individual contact under its company, tagged "Esterno".
 */
class ExternalContactController
{
    public const TAG = 'Esterno';

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Partner::class);
        $data = $this->validated($request);
        $partner = Partner::query()->where('account_type', AccountType::INDIVIDUAL)->whereRaw('LOWER(email) = ?', [$data['email']])->first();

        return $this->save($partner ?? new Partner, $data, $partner ? 200 : 201);
    }

    public function update(Request $request, Partner $partner): JsonResponse
    {
        Gate::authorize('update', $partner);

        return $this->save($partner, $this->validated($request), 200);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name'  => ['required', 'string', 'max:120'],
            'company'    => ['required', 'string', 'max:200'],
            'email'      => ['required', 'email', 'max:255'],
        ]);
        $data = array_map(fn (string $v): string => trim(preg_replace('/\s+/u', ' ', $v)), $data);
        $data['email'] = mb_strtolower($data['email']);

        return $data;
    }

    private function save(Partner $partner, array $data, int $status): JsonResponse
    {
        DB::transaction(function () use ($partner, $data): void {
            $company = Partner::query()->where('account_type', AccountType::COMPANY)->whereRaw('LOWER(name) = ?', [mb_strtolower($data['company'])])->first()
                ?? Partner::query()->create(['account_type' => AccountType::COMPANY, 'name' => $data['company']]);
            $partner->fill([
                'account_type' => AccountType::INDIVIDUAL,
                'name'         => $data['first_name'].' '.$data['last_name'],
                'email'        => $data['email'],
                'parent_id'    => $company->getKey(),
            ])->save();
            $tag = Tag::query()->firstOrCreate(['name' => self::TAG]);
            $partner->tags()->syncWithoutDetaching([$tag->getKey()]);
            $company->tags()->syncWithoutDetaching([$tag->getKey()]);
        });
        $partner->refresh();

        return response()->json(['data' => [
            'id'      => $partner->getKey(), 'name' => $partner->name, 'email' => $partner->email,
            'company' => $data['company'], 'parent_id' => $partner->parent_id, 'updated_at' => $partner->updated_at?->toIso8601String(),
        ]], $status);
    }
}
