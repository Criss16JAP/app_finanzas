<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->constrained('credits')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2); // Total pagado
            $table->decimal('principal_amount', 15, 2); // Cuánto fue a capital
            $table->decimal('interest_amount', 15, 2); // Cuánto fue a intereses
            $table->decimal('fee_amount', 15, 2); // Cuánto fue a cargos fijos

            // Enlace al Egreso que se creó en Módulo 1
            $table->foreignId('payment_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_payments');
    }
};
