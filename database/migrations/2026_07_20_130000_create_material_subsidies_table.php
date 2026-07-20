<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_subsidies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->date('subsidy_date');
            $table->enum('status', ['draft', 'confirmed'])->default('draft');
            $table->foreignUuid('regional_police_id')->constrained('regional_police')->cascadeOnDelete();
            $table->string('recipient_name', 255); // nama penerima bebas
            $table->text('recipient_description')->nullable(); // keterangan penerima
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignUuid('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['code', 'status', 'regional_police_id']);
            $table->index(['deleted_at', 'created_at', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_subsidies');
    }
};
