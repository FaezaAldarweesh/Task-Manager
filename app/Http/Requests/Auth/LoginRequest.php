<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * This method is called before validation starts to clean or normalize inputs.
     * 
     * Convert email to lowercase and trim white spaces if provided
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|max:30',
        ];
    }
    /**
     * Define human-readable attribute names for validation errors.
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'email address',
            'password' => 'password',
        ];
    }

    /**
     * Define custom error messages for validation failures.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email' => 'The :attribute must be a valid email address.',
            'required' => 'The :attribute field is required.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be greater than :max characters.',
        ];
    }

    /**
     * Handle validation errors and throw an exception.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator The validation instance.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(
            ApiResponseService::error($errors, 'A server error has occurred', 403)
        );
    }

    /**
     * Custom validation logic after the request has passed validation.
     * 
     * @return void
     */
    protected function passedValidation()
    {
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            throw new HttpResponseException(
                ApiResponseService::error(['The provided email does not exist.'], 'A server error has occurred', 401)
            );
        }

        if (!Hash::check($this->password, $user->password)) {
            throw new HttpResponseException(
                ApiResponseService::error(['The provided password is incorrect.'], 'A server error has occurred', 401)
            );
        }
    }
}
