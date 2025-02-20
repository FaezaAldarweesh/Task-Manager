<?php

namespace App\Http\Requests\Task;

use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
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
     * Capitalize the first letter and trim white spaces if provided
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'title' => $this->title ? ucwords(trim($this->title)) : null,
            'due_date' => $this->due_date ? date('Y-m-d', strtotime($this->due_date)) : null, // Corrected this line
            'created_by' => Auth::id(),
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
            'title' => 'nullable|string|max:100|min:2',
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|in:bug,feature,improvement',
            'status' => 'nullable|in:open,in_progress,completed,blocked',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => 'nullable|exists:users,id',
            'created_by' => 'nullable|exists:users,id',
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
            'title' => 'task title',
            'description' => 'task description',
            'type' => 'task type',
            'status' => 'task status',
            'priority' => 'task priority',
            'due_date' => 'due date',
            'assigned_to' => 'assigned user',
            'created_by' => 'creator',
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
            'required' => 'The :attribute field is required.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'in' => 'The selected :attribute is invalid.',
            'date' => 'The :attribute must be a valid date.',
            'after_or_equal' => 'The :attribute must be today or a future date.',
            'exists' => 'The selected :attribute is invalid.',
            'min' => 'The :attribute must be at least :min characters.',
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
}
