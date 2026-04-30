<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds adjustment tracking columns to cash_flows table.
     *
     * - status: 'approved' by default so all existing records are unaffected.
     * - is_adjustment: flags records generated from shift closing discrepancies.
     * - shift_id: links the discrepancy back to the specific shift for audit trail.
     * - rejection_reason: allows Superadmin to provide a reason when rejecting.
     */
    public function up(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->after('note');

            $table->boolean('is_adjustment')
                  ->default(false)
                  ->after('status');

            $table->foreignId('shift_id')
                  ->nullable()
                  ->after('is_adjustment')
                  ->constrained('cashier_shifts')
                  ->nullOnDelete();

            $table->text('rejection_reason')
                  ->nullable()
                  ->after('shift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn(['status', 'is_adjustment', 'shift_id', 'rejection_reason']);
        });
    }
};
