<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers'      => Customer::where('status', 'active')->count(),
            'total_projects'       => Project::count(),
            'total_invoice_amount' => Invoice::whereNotIn('status', ['cancelled'])->sum('final_amount'),
            'total_received'       => Payment::sum('amount'),
            'total_due'            => Invoice::whereNotIn('status', ['cancelled'])->sum('final_amount')
                                   - Payment::sum('amount'),
        ];

        $recentInvoices = Invoice::with(['project.customer'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentInvoices'));
    }
}