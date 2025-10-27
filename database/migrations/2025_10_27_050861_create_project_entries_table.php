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
        Schema::create('project_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->enum('type', ['Cost', 'Received']); // Costo o Recibido
            $table->decimal('amount', 15, 2);
            $table->string('description');

            // El enlace CLAVE al Módulo 1 (Ingreso o Egreso)
            $table->foreignId('linked_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_entries');
    }
};
