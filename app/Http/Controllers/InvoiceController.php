<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use App\Services\InvoiceService;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function index()
    {
        $invoices = Invoice::with('project.customer')
            ->when(request('name'), fn($q) => $q->where(function($q) {
                $q->where('invoice_number', 'like', '%' . request('name') . '%')
                  ->orWhereHas('project', fn($q) =>
                      $q->where('project_name', 'like', '%' . request('name') . '%')
                  )
                  ->orWhereHas('project.customer', fn($q) =>
                      $q->where('name', 'like', '%' . request('name') . '%')
                  );
            }))
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->latest()
            ->paginate(request('per_page', 15));

        return retRes('', $invoices, 2000);
    }

    public function store(StoreInvoiceRequest $request)
    {
        $project = Project::findOrFail($request->project_id);
        $invoice = $this->invoiceService->createInvoice($project, $request->validated());

        return retRes('Invoice created successfully.', $invoice, 2000);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['project.customer', 'payments']);

        return retRes('', $invoice, 2000);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        if (! $invoice->isEditable()) {
            return retRes('Invoice cannot be edited after payment is received.', null, 4000);
        }

        $this->invoiceService->updateInvoice($invoice, $request->validated());

        return retRes('Invoice updated successfully.', null, 2000);
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->hasPayments()) {
            return retRes('Cannot delete invoice with existing payments.', null, 4000);
        }

        $invoice->delete();

        return retRes('Invoice deleted successfully.', null, 2000);
    }

    public function cancel(Invoice $invoice)
    {
        $this->invoiceService->cancelInvoice($invoice);

        return retRes('Invoice cancelled successfully.', null, 2000);
    }
}