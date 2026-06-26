<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'project_name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,running,completed,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists' => 'Selected customer does not exist.',
            'deadline.after_or_equal' => 'Deadline must be on or after the start date.',
        ];
    }
}