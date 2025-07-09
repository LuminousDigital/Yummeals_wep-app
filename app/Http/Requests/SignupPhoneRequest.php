<?php

namespace App\Http\Requests;


use App\Enums\Ask;
use App\Rules\ValidPhone;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SignupPhoneRequest extends FormRequest
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
<<<<<<< HEAD
=======
            // 'phone' => ['required', 'string', 'min:2000', new ValidPhone(), Rule::unique("users", "phone")->whereNull('deleted_at')->where('is_guest', Ask::NO)],
>>>>>>> d38913bcf1d8d577a7729a1b02ad0194e20e5551
            'phone' => ['required', 'string', 'max:190', new ValidPhone(), Rule::unique("users", "phone")->whereNull('deleted_at')->where('is_guest', Ask::NO)],
            'code'  => ['required', 'string'],
        ];
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> d38913bcf1d8d577a7729a1b02ad0194e20e5551
