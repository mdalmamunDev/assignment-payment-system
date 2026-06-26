<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->string('invoice_number')->unique(); // INV-2026-0001
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('invoice_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2);    // auto-calculated
            $table->decimal('paid_amount', 15, 2)->default(0); // updated on payment
            $table->enum('status', ['draft', 'sent', 'partially_paid', 'paid', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
