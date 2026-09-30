<?php

use Huvant\Bridge\Http\Controllers\API\V1\EmployeeController;
use Huvant\Bridge\Http\Controllers\API\V1\NoteController;
use Huvant\Bridge\Http\Controllers\API\V1\ProjectColorController;
use Huvant\Bridge\Http\Controllers\API\V1\TimesheetController;
use Huvant\Bridge\Http\Controllers\API\V1\UserController;
use Huvant\Bridge\Http\Middleware\EnsureActiveUser;
use Huvant\Bridge\Http\Middleware\EnsureIdempotentBridgeRequest;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::name('admin.api.v1.huvant.')
    ->prefix('admin/api/v1/huvant')
    ->middleware(['auth:sanctum', EnsureActiveUser::class, SubstituteBindings::class])
    ->group(function (): void {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('users', [UserController::class, 'store'])->middleware(EnsureIdempotentBridgeRequest::class)->name('users.store');
        Route::match(['put', 'patch'], 'users/{user}', [UserController::class, 'update'])->middleware(EnsureIdempotentBridgeRequest::class)->name('users.update');
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('timesheets', [TimesheetController::class, 'index'])->name('timesheets.index');
        Route::patch('projects/{project}/color', [ProjectColorController::class, 'update'])->middleware(EnsureIdempotentBridgeRequest::class)->name('projects.color.update');
        Route::post('projects/{project}/notes', [NoteController::class, 'project'])->middleware(EnsureIdempotentBridgeRequest::class)->name('projects.notes.store');
        Route::post('tasks/{task}/notes', [NoteController::class, 'task'])->middleware(EnsureIdempotentBridgeRequest::class)->name('tasks.notes.store');
    });
