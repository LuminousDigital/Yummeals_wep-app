<?php

namespace App\Http\Requests;

use App\Enums\Ask;
use App\Rules\ValidPhone;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => ['nullable', 'string', 'email', 'max:255', Rule::unique("users", "email")->whereNull('deleted_at')->where('is_guest', Ask::NO)],
            'phone'        => ['required', 'numeric', new ValidPhone(), Rule::unique("users", "phone")->whereNull('deleted_at')->where('is_guest', Ask::NO)],
            'country_code' => ['required', 'numeric'],
            'password'     => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
        ];
    }

    /**
     * Get the custom validation messages for errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.required'   => 'First name is required.',
            'first_name.string'     => 'First name must be a valid string.',
            'first_name.max'        => 'First name must not exceed 255 characters.',

            'last_name.required'    => 'Last name is required.',
            'last_name.string'      => 'Last name must be a valid string.',
            'last_name.max'         => 'Last name must not exceed 255 characters.',

            'email.string'          => 'Email must be a valid string.',
            'email.email'           => 'Please enter a valid email address.',
            'email.max'             => 'Email must not exceed 255 characters.',
            'email.unique'          => 'This email address has already been taken.',

            'phone.required'        => 'Phone number is required.',
            'phone.numeric'         => 'Phone number must contain only numbers.',
            'phone.unique'          => 'This phone number has already been taken.',

            'country_code.required' => 'Country code is required.',
            'country_code.numeric'  => 'Country code must be numeric.',

            'password.required'     => 'Password is required.',
            'password.string'       => 'Password must be a valid string.',
            'password.min'          => 'Password must be at least 8 characters long.',
            'password.regex'        => 'Password must contain at least one letter and one number.',
        ];
    }
}
