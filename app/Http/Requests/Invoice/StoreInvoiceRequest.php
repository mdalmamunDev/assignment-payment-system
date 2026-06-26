<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'project_id'      => ['required', 'exists:projects,id'],
            'invoice_date'    => ['required', 'date'],
            'due_date'        => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'invoice_amount'  => ['required', 'numeric', 'min:0.01'],
            'tax_amount'      => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_amount.min'      => 'Invoice amount must be greater than zero.',
            'due_date.after_or_equal' => 'Due date must be on or after the invoice date.',
        ];
    }

    // convert nullable fields to 0 before validation runs
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tax_amount'      => $this->tax_amount ?? 0,
            'discount_amount' => $this->discount_amount ?? 0,
        ]);
    }
}