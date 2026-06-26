<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Payment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $inv1 = Invoice::where('invoice_number', 'INV-2026-0001')->first(); // paid
        $inv2 = Invoice::where('invoice_number', 'INV-2026-0002')->first(); // partially paid
        $inv4 = Invoice::where('invoice_number', 'INV-2026-0004')->first(); // paid
        $inv5 = Invoice::where('invoice_number', 'INV-2026-0005')->first(); // partially paid

        $payments = [
            // INV-0001 — two payments adding up to full amount
            [
                'invoice_id'     => $inv1->id,
                'payment_date'   => '2026-01-20',
                'amount'         => 30000.00,
                'payment_method' => 'bank',
                'reference_no'   => 'TXN-001',
                'note'           => 'First installment',
            ],
            [
                'invoice_id'     => $inv1->id,
                'payment_date'   => '2026-02-10',
                'amount'         => 21500.00,
                'payment_method' => 'mobile_banking',
                'reference_no'   => 'TXN-002',
                'note'           => 'Final payment',
            ],

            // INV-0002 — one partial payment
            [
                'invoice_id'     => $inv2->id,
                'payment_date'   => '2026-03-10',
                'amount'         => 30000.00,
                'payment_method' => 'cash',
                'reference_no'   => null,
                'note'           => 'Advance payment',
            ],

            // INV-0004 — single full payment
            [
                'invoice_id'     => $inv4->id,
                'payment_date'   => '2026-02-20',
                'amount'         => 75000.00,
                'payment_method' => 'bank',
                'reference_no'   => 'TXN-003',
                'note'           => 'Full payment',
            ],

            // INV-0005 — one partial payment
            [
                'invoice_id'     => $inv5->id,
                'payment_date'   => '2026-03-01',
                'amount'         => 50000.00,
                'payment_method' => 'bank',
                'reference_no'   => 'TXN-004',
                'note'           => 'First milestone payment',
            ],
        ];

        foreach ($payments as $payment) {
            Payment::create($payment);
        }
    }
}