<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rack_assignments', function (Blueprint $table) {
            // Drop old item-specific columns (moved to rack_assignment_details)
            $table->dropColumn([
                'stock_detail_id',
                'type_id',
                'type_detail_id',
                'from_rack_id',
                'to_rack_id',
                'item_code',
                'number_serial_first',
                'number_serial_second',
                'quantity',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rack_assignments', function (Blueprint $table) {
            // Restore columns if needed to rollback
            $table->uuid('stock_detail_id')->nullable();
            $table->uuid('type_id')->nullable();
            $table->uuid('type_detail_id')->nullable();
            $table->uuid('from_rack_id')->nullable();
            $table->uuid('to_rack_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('number_serial_first')->nullable();
            $table->string('number_serial_second')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
        });
    }
};
