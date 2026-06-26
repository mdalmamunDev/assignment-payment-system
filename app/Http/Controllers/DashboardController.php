<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        $result = [
            'total_customers'      => Customer::where('status', 'active')->count(),
            'total_projects'       => Project::count(),
            'total_invoice_amount' => Invoice::whereNotIn('status', ['cancelled'])->sum('final_amount'),
            'total_received'       => Payment::sum('amount'),
            'total_due'            => Invoice::whereNotIn('status', ['cancelled'])->sum('final_amount') - Payment::sum('amount'),
        ];

        return retRes('', $result, 2000);
    }
}