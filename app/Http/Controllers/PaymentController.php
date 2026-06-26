<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Http\Requests\Payment\StorePaymentRequest;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function store(StorePaymentRequest $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $this->paymentService->savePayment($invoice, $request->validated());

        return retRes('Payment recorded successfully.', null, 2000);
    }

    public function destroy(Payment $payment)
    {
        $this->paymentService->deletePayment($payment);

        return retRes('Payment deleted successfully.', null, 2000);
    }
}