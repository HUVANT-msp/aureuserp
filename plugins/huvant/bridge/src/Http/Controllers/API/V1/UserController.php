<?php

namespace Huvant\Bridge\Http\Controllers\API\V1;

use Huvant\Bridge\Http\Requests\IndexUsersRequest;
use Huvant\Bridge\Http\Requests\StoreUserRequest;
use Huvant\Bridge\Http\Requests\UpdateUserRequest;
use Huvant\Bridge\Http\Resources\V1\UserResource;
use Huvant\Bridge\Support\BridgeAuthorization;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

class UserController
{
    public function index(IndexUsersRequest $request)
    {
        Gate::authorize('viewAny', User::class);

        $data = $request->validated();
        $query = User::query()->with(['allowedCompanies', 'roles']);

        if (! BridgeAuthorization::isSuperAdmin($request->user())) {
            $query->whereIn('default_company_id', BridgeAuthorization::companyIds($request->user()));
        }

        $query->when($data['email'] ?? null, fn ($query, $email) => $query->where('email', $email));
        $query->when(array_key_exists('is_active', $data), fn ($query) => $query->where('is_active', $data['is_active']));
        $query->when($data['updated_after'] ?? null, fn ($query, $date) => $query->where('updated_at', '>', $date));

        return UserResource::collection($query->orderBy('id')->paginate($data['per_page'] ?? 25));
    }

    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create(Arr::only($data, [
                'name', 'email', 'password', 'language', 'is_active', 'default_company_id', 'resource_permission',
            ]) + [
                'is_active'           => $data['is_active'] ?? true,
                'resource_permission' => $data['resource_permission'] ?? PermissionType::INDIVIDUAL->value,
            ]);

            $user->allowedCompanies()->sync($data['allowed_company_ids']);
            $user->roles()->sync($data['role_ids'] ?? []);

            return $user->load(['allowedCompanies', 'roles']);
        });

        return (new UserResource($user))
            ->additional(['message' => 'User created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        if (! BridgeAuthorization::isSuperAdmin(request()->user())
            && ! BridgeAuthorization::companyIds(request()->user())->contains((int) $user->default_company_id)) {
            abort(403);
        }

        return new UserResource($user->load(['allowedCompanies', 'roles']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $data = $request->validated();

        DB::transaction(function () use ($user, $data): void {
            $user->update(Arr::only($data, [
                'name', 'email', 'password', 'language', 'is_active', 'default_company_id', 'resource_permission',
            ]));

            if (array_key_exists('allowed_company_ids', $data)) {
                $user->allowedCompanies()->sync($data['allowed_company_ids']);
            }

            if (array_key_exists('role_ids', $data)) {
                $user->roles()->sync($data['role_ids']);
            }
        });

        return (new UserResource($user->refresh()->load(['allowedCompanies', 'roles'])))
            ->additional(['message' => 'User updated successfully.']);
    }
}
