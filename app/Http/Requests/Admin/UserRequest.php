<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('put')) {

            return [
                'name' => ['required', 'string', 'max:255'],
                'mobile' => ['required', 'digits_between:9,12', Rule::unique('users', 'mobile')->ignore($this->id)],
                'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
                'password' => ['nullable', 'string', 'min:8'],
                'status' => ['required', 'in:0,1'],
            ];
        }

        return [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'mobile' => ['required', 'digits_between:9,12', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:0,1'],
        ];
    }
}
