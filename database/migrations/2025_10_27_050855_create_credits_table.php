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
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->decimal('initial_amount', 15, 2); // Monto desembolsado
            $table->decimal('outstanding_balance', 15, 2); // Saldo pendiente (capital)
            $table->float('interest_rate'); // Tasa de interés mensual (ej. 1.5 para 1.5%)
            $table->decimal('fixed_fee_amount', 15, 2)->default(0); // Cargos fijos (ej. seguros)
            $table->unsignedTinyInteger('payment_day'); // Día del mes para pago (ej. 5)
            $table->unsignedInteger('total_installments'); // Plazo total en meses

            // Enlace al Ingreso que se creó en Módulo 1
            $table->foreignId('disbursement_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
