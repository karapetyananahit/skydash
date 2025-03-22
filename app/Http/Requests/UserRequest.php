<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->route('id')],
            'profile_img' => ['string', 'nullable'],
        ];

        if ($this->isMethod('post')) { // Եթե store (create) request է
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        } else { // Եթե update (edit) request է
            $rules['password'] = ['nullable', 'confirmed', Password::defaults()];
        }

        return $rules;
    }
}
