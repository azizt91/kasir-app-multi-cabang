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
        // Using raw SQL to ensure compatibility without doctrine/dbal
        DB::statement("ALTER TABLE products MODIFY stock DECIMAL(10,2) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transaction_items MODIFY quantity DECIMAL(10,2) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY stock INT NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE transaction_items MODIFY quantity INT NOT NULL");
    }
};
