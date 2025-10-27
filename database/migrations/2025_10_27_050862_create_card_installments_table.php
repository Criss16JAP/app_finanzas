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
        Schema::create('card_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smart_credit_card_id')->constrained('smart_credit_cards')->onDelete('cascade');

            // Enlace al Egreso (compra) de Módulo 1
            $table->foreignId('original_transaction_id')->constrained('transactions')->onDelete('cascade');

            $table->decimal('total_amount', 15, 2);
            $table->unsignedInteger('installments_number'); // Número de cuotas
            $table->unsignedInteger('installments_paid')->default(0);
            $table->decimal('monthly_payment', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_installments');
    }
};
