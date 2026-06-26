<?php
namespace App\Services;

use App\Models\Invoice;
use App\Models\Project;

class InvoiceService
{
  // format: INV-2026-0001
  public function generateInvoiceNumber(): string
  {
    $year = now()->year;
    $prefix = "INV-{$year}-";

    $last = Invoice::where('invoice_number', 'like', "{$prefix}%")
      ->orderByDesc('id')
      ->first();

    if ($last) {
      $lastNumber = (int) str_replace($prefix, '', $last->invoice_number);
      $next = $lastNumber + 1;
    } else {
      $next = 1;
    }

    return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
  }

  // final amount = invoice + tax - discount
  public function calculateFinalAmount(
    float $invoiceAmount,
    float $taxAmount = 0,
    float $discountAmount = 0
  ): float {
    return $invoiceAmount + $taxAmount - $discountAmount;
  }

  public function createInvoice(Project $project, array $data): Invoice
  {
    $finalAmount = $this->calculateFinalAmount(
      (float) $data['invoice_amount'],
      (float) ($data['tax_amount'] ?? 0),
      (float) ($data['discount_amount'] ?? 0),
    );

    return Invoice::create([
      'project_id' => $project->id,
      'invoice_number' => $this->generateInvoiceNumber(),
      'invoice_date' => $data['invoice_date'],
      'due_date' => $data['due_date'] ?? null,
      'invoice_amount' => $data['invoice_amount'],
      'tax_amount' => $data['tax_amount'] ?? 0,
      'discount_amount' => $data['discount_amount'] ?? 0,
      'final_amount' => $finalAmount,
      'paid_amount' => 0,
      'status' => 'draft',
    ]);
  }


  public function updateInvoice(Invoice $invoice, array $data): Invoice
  {
    $finalAmount = $this->calculateFinalAmount(
      (float) $data['invoice_amount'],
      (float) ($data['tax_amount'] ?? 0),
      (float) ($data['discount_amount'] ?? 0),
    );

    $invoice->update([
      'invoice_date' => $data['invoice_date'],
      'due_date' => $data['due_date'] ?? null,
      'invoice_amount' => $data['invoice_amount'],
      'tax_amount' => $data['tax_amount'] ?? 0,
      'discount_amount' => $data['discount_amount'] ?? 0,
      'final_amount' => $finalAmount,
    ]);

    return $invoice->fresh();
  }


  public function cancelInvoice(Invoice $invoice): void
  {
    abort_if($invoice->hasPayments(), 422, 'Cannot cancel invoice with existing payments.');

    $invoice->update(['status' => 'cancelled']);
  }
}