<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Change enum to string to allow 'void' status
            // Note: We need to use DB::statement for changing column type if doctrine/dbal is not installed or for enum conversion.
            // Since we are moving from ENUM to STRING (larger set), it's safe.
            // However, verify if 'change()' works without doctrine/dbal. It usually requires it.
            // Let's use raw SQL for safety and minimal dependencies.
            
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status VARCHAR(20) DEFAULT 'completed'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert back to enum (optional, might fail if 'void' exists)
             DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('completed', 'cancelled') DEFAULT 'completed'");
        });
    }
};
