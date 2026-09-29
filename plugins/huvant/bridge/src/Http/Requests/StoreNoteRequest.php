<?php

namespace Huvant\Bridge\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $record = $this->route('project') ?? $this->route('task');

        return $record !== null && Gate::allows('update', $record);
    }

    public function rules(): array
    {
        return [
            'body'          => ['required', 'string', 'max:100000'],
            'subject'       => ['nullable', 'string', 'max:255'],
            'is_internal'   => ['sometimes', 'boolean'],
            'attachments'   => ['prohibited'],
        ];
    }
}
