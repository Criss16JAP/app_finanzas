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
        Schema::create('smart_credit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->decimal('credit_limit', 15, 2); // Cupo total
            $table->decimal('current_balance', 15, 2)->default(0); // Deuda actual
            $table->float('interest_rate'); // Tasa de interés mensual
            $table->unsignedTinyInteger('statement_day'); // Día de corte (ej. 25)
            $table->unsignedTinyInteger('payment_day'); // Día de pago (ej. 10)
            $table->decimal('monthly_fee', 15, 2)->default(0); // Cuota de manejo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_credit_cards');
    }
};
