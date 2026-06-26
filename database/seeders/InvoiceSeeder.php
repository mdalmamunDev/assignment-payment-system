<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        Invoice::truncate();

        $ecommerce = Project::where('project_code', 'PRJ-2026-0001')->first();
        $mobile    = Project::where('project_code', 'PRJ-2026-0002')->first();
        $inventory = Project::where('project_code', 'PRJ-2026-0003')->first();
        $hr        = Project::where('project_code', 'PRJ-2026-0004')->first();

        $invoices = [
            // E-commerce — fully paid
            [
                'project_id'      => $ecommerce->id,
                'invoice_number'  => 'INV-2026-0001',
                'invoice_date'    => '2026-01-15',
                'due_date'        => '2026-02-15',
                'invoice_amount'  => 50000.00,
                'tax_amount'      => 2500.00,
                'discount_amount' => 1000.00,
                'final_amount'    => 51500.00,
                'paid_amount'     => 51500.00,
                'status'          => 'paid',
            ],
            // E-commerce — partially paid
            [
                'project_id'      => $ecommerce->id,
                'invoice_number'  => 'INV-2026-0002',
                'invoice_date'    => '2026-03-01',
                'due_date'        => '2026-04-01',
                'invoice_amount'  => 60000.00,
                'tax_amount'      => 3000.00,
                'discount_amount' => 0.00,
                'final_amount'    => 63000.00,
                'paid_amount'     => 30000.00,
                'status'          => 'partially_paid',
            ],
            // Mobile App — sent, no payment yet
            [
                'project_id'      => $mobile->id,
                'invoice_number'  => 'INV-2026-0003',
                'invoice_date'    => '2026-03-05',
                'due_date'        => '2026-04-05',
                'invoice_amount'  => 80000.00,
                'tax_amount'      => 4000.00,
                'discount_amount' => 2000.00,
                'final_amount'    => 82000.00,
                'paid_amount'     => 0.00,
                'status'          => 'sent',
            ],
            // Inventory — fully paid
            [
                'project_id'      => $inventory->id,
                'invoice_number'  => 'INV-2026-0004',
                'invoice_date'    => '2026-02-01',
                'due_date'        => '2026-03-01',
                'invoice_amount'  => 80000.00,
                'tax_amount'      => 0.00,
                'discount_amount' => 5000.00,
                'final_amount'    => 75000.00,
                'paid_amount'     => 75000.00,
                'status'          => 'paid',
            ],
            // HR — partially paid
            [
                'project_id'      => $hr->id,
                'invoice_number'  => 'INV-2026-0005',
                'invoice_date'    => '2026-02-15',
                'due_date'        => '2026-03-15',
                'invoice_amount'  => 100000.00,
                'tax_amount'      => 5000.00,
                'discount_amount' => 0.00,
                'final_amount'    => 105000.00,
                'paid_amount'     => 50000.00,
                'status'          => 'partially_paid',
            ],
            // HR — draft
            [
                'project_id'      => $hr->id,
                'invoice_number'  => 'INV-2026-0006',
                'invoice_date'    => '2026-04-01',
                'due_date'        => null,
                'invoice_amount'  => 60000.00,
                'tax_amount'      => 3000.00,
                'discount_amount' => 1500.00,
                'final_amount'    => 61500.00,
                'paid_amount'     => 0.00,
                'status'          => 'draft',
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::create($invoice);
        }
    }
}