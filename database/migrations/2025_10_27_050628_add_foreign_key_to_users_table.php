<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Primero creamos la columna si no existe
            if (!Schema::hasColumn('users', 'current_plan_id')) {
                $table->unsignedBigInteger('current_plan_id')->nullable()->after('id');
            }

            // 2. Luego agregamos la llave foránea
            $table->foreign('current_plan_id')
                ->references('id')
                ->on('plans')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
