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
        Schema::create(config('starup.db_prefix') . 'badge_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained(config('starup.db_prefix') . 'badges')->onDelete('cascade');
            $table->string('criterion_type');
            $table->string('criterion_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('starup.db_prefix') . 'badge_rules');
    }
};
