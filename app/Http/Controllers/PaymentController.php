<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Http\Requests\Payment\StorePaymentRequest;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function create(Invoice $invoice)
    {
        if (!$invoice->canReceivePayment()) {
            return back()->with('error', 'This invoice cannot receive payment.');
        }

        $invoice->load(['project.customer']);

        return view('payments.create', compact('invoice'));
    }

    public function store(StorePaymentRequest $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        $this->paymentService->savePayment($invoice, $request->validated());

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully.');
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;

        $this->paymentService->deletePayment($payment);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment deleted successfully.');
    }
}