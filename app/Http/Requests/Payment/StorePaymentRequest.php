<?php

namespace App\Http\Requests\Payment;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,bank,mobile_banking'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    // extra business rule validation after basic rules pass
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $invoice = Invoice::find($this->invoice_id);

                if (!$invoice)
                    return;

                if ($invoice->isCancelled()) {
                    $validator->errors()->add(
                        'invoice_id',
                        'Cannot add payment to a cancelled invoice.'
                    );
                }

                if ((float) $this->amount > $invoice->due_amount) {
                    $validator->errors()->add(
                        'amount',
                        "Payment amount cannot exceed due amount of {$invoice->due_amount}."
                    );
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Payment amount must be greater than zero.',
            'payment_method.in' => 'Payment method must be Cash, Bank, or Mobile Banking.',
        ];
    }
}