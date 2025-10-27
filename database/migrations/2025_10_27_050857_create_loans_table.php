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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('borrower_name'); // Nombre del deudor
            $table->decimal('initial_amount', 15, 2);
            $table->decimal('outstanding_balance', 15, 2);
            $table->float('interest_rate');
            $table->enum('interest_type', ['Fijo', 'Porcentual']);

            // Enlace al Egreso (categoría "Préstamos")
            $table->foreignId('disbursement_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
