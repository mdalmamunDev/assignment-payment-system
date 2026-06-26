<?php
// app/Services/PaymentService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{

  public function savePayment(Invoice $invoice, array $data): Payment
  {
    // guard: cancelled invoice cannot receive payment
    if ($invoice->isCancelled()) {
      abort(422, 'Cannot add payment to a cancelled invoice.');
    }

    // guard: payment cannot exceed due amount
    $dueAmount = $invoice->due_amount;
    if ((float) $data['amount'] > $dueAmount) {
      abort(422, "Payment amount cannot exceed due amount of {$dueAmount}.");
    }

    return DB::transaction(function () use ($invoice, $data) {

      // 1. create the payment record
      $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'payment_date' => $data['payment_date'],
        'amount' => $data['amount'],
        'payment_method' => $data['payment_method'],
        'reference_no' => $data['reference_no'] ?? null,
        'note' => $data['note'] ?? null,
      ]);

      // 2. update invoice paid_amount
      $newPaidAmount = (float) $invoice->paid_amount + (float) $data['amount'];
      $invoice->paid_amount = $newPaidAmount;

      // 3. update invoice status automatically
      $invoice->status = $this->resolveInvoiceStatus(
        $newPaidAmount,
        (float) $invoice->final_amount
      );

      $invoice->save();

      return $payment;
    });
  }

  // auto-resolve invoice status after payment

  private function resolveInvoiceStatus(float $paidAmount, float $finalAmount): string
  {
    if ($paidAmount <= 0) {
      return 'sent';
    }

    if ($paidAmount >= $finalAmount) {
      return 'paid';
    }

    return 'partially_paid';
  }

  // delete payment (recalculate invoice)
  public function deletePayment(Payment $payment): void
  {
    DB::transaction(function () use ($payment) {
      $invoice = $payment->invoice;

      // subtract this payment from paid_amount
      $newPaidAmount = max(0, (float) $invoice->paid_amount - (float) $payment->amount);
      $invoice->paid_amount = $newPaidAmount;
      $invoice->status = $this->resolveInvoiceStatus(
        $newPaidAmount,
        (float) $invoice->final_amount
      );
      $invoice->save();

      $payment->delete();
    });
  }
}