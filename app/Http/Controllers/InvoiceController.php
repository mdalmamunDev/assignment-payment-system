<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use App\Services\InvoiceService;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {
    }

    public function index()
    {
        $invoices = Invoice::query()
            ->with(['project.customer'])
            ->when(request('search'), function ($q) {
                $q->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . request('search') . '%')
                        ->orWhereHas(
                            'project',
                            fn($q) =>
                            $q->where('project_name', 'like', '%' . request('search') . '%')
                        )
                        ->orWhereHas(
                            'project.customer',
                            fn($q) =>
                            $q->where('name', 'like', '%' . request('search') . '%')
                        );
                });
            })
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $projects = Project::with('customer')
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('project_name')
            ->get();

        return view('invoices.create', compact('projects'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $project = Project::findOrFail($request->project_id);

        $this->invoiceService->createInvoice($project, $request->validated());

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['project.customer', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if (!$invoice->isEditable()) {
            return back()->with('error', 'Invoice cannot be edited after payment is received.');
        }

        $projects = Project::with('customer')
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('project_name')
            ->get();

        return view('invoices.edit', compact('invoice', 'projects'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        if (!$invoice->isEditable()) {
            return back()->with('error', 'Invoice cannot be edited after payment is received.');
        }

        $this->invoiceService->updateInvoice($invoice, $request->validated());

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->hasPayments()) {
            return back()->with('error', 'Cannot delete invoice with existing payments.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function cancel(Invoice $invoice)
    {
        $this->invoiceService->cancelInvoice($invoice);

        return back()->with('success', 'Invoice cancelled successfully.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['project.customer', 'payments']);

        return view('invoices.print', compact('invoice'));
    }
}