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
        Schema::table('branches', function (Blueprint $table) {
            $table->text('receipt_footer')->nullable()->after('phone');
            $table->enum('paper_size', ['58', '80'])->default('58')->after('receipt_footer');
            $table->string('logo')->nullable()->after('paper_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['receipt_footer', 'paper_size', 'logo']);
        });
    }
};
