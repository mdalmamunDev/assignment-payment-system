<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;

class ReportController extends Controller
{
    public function dueReport()
    {
        $invoices = Invoice::with('project.customer')
            ->when(request('customer_id'), fn($q) =>
                $q->whereHas('project', fn($q) =>
                    $q->where('customer_id', request('customer_id'))
                )
            )
            ->when(request('project_id'), fn($q) => $q->where('project_id', request('project_id')))
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->when(request('date_from'), fn($q) => $q->whereDate('invoice_date', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('invoice_date', '<=', request('date_to')))
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->paginate(request('per_page', 15));

        return retRes('', $invoices, 2000);
    }
}