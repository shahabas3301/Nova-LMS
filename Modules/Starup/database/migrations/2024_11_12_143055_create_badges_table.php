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
        Schema::create(config('starup.db_prefix') . 'badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained(config('starup.db_prefix') . 'badge_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('starup.db_prefix') . 'badges');
    }
};
