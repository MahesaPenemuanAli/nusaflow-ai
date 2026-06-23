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
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->date('visit_date');
            $table->tinyInteger('visit_hour')->nullable();
            $table->unsignedInteger('visitor_count')->default(0);
            $table->string('weather', 50)->nullable();
            $table->string('source')->default('admin_input');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['destination_id', 'visit_date', 'visit_hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
