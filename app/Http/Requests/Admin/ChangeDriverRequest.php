<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChangeDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'driver_nik' => ['required', 'string', 'exists:LgiGlobal114.users,NIK'],
        ];
    }
}
