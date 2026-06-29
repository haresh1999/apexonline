<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
                'client_id' => ['required', Rule::unique('users', 'client_id')->ignore($this->id)],
                'client_secret' => ['required', 'uuid', Rule::unique('users', 'client_secret')->ignore($this->id)],
                'sbx_client_id' => ['nullable', Rule::unique('users', 'sbx_client_id')->ignore($this->id)],
                'sbx_client_secret' => ['nullable', 'uuid', Rule::unique('users', 'sbx_client_secret')->ignore($this->id)],
                'whitelist_ip' => ['nullable', 'string'],
                'default_gateway' => ['nullable', 'string'],
                'callback_secret' => ['required', 'uuid', Rule::unique('users', 'callback_secret')->ignore($this->id)],
                'password' => ['nullable', 'string', 'min:8'],
                'status' => ['required', 'in:0,1'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'digits_between:9,12', 'unique:users,mobile'],
            'email' => ['required', 'email', 'unique:users,email'],
            'client_id' => ['required', 'unique:users,client_id'],
            'client_secret' => ['required', 'uuid', 'unique:users,client_secret'],
            'sbx_client_id' => ['nullable', 'unique:users,sbx_client_id',],
            'sbx_client_secret' => ['nullable', 'uuid', 'unique:users,sbx_client_secret'],
            'whitelist_ip' => ['nullable', 'string'],
            'default_gateway' => ['nullable', 'string'],
            'callback_secret' => ['required', 'uuid', 'unique:users,callback_secret'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:0,1'],
        ];
    }
}
