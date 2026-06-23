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
        Schema::create('crowd_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->date('prediction_date');
            $table->tinyInteger('prediction_hour')->nullable();
            $table->unsignedInteger('predicted_count')->default(0);
            $table->decimal('crowd_score', 5, 2)->nullable();
            $table->string('crowd_level')->default('low');
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->string('method')->default('rule_based');
            $table->string('model_version', 50)->default('rule-based-v1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crowd_predictions');
    }
};
