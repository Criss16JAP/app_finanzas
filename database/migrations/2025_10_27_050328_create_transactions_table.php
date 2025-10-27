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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');

            $table->enum('type', ['Ingreso', 'Egreso', 'Transferencia']);
            $table->decimal('amount', 15, 2); // Siempre positivo
            $table->text('description')->nullable();
            $table->timestamp('transaction_date')->useCurrent();

            // Para enlazar movimientos (ej. 4x1000 al egreso original)
            $table->foreignId('parent_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');

            // Para transferencias: De dónde vino / A dónde fue
            $table->foreignId('related_account_id')->nullable()->constrained('accounts')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
