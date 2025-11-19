<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create(config('forumwise.db.prefix').'media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mediaable_id')->index();
            $table->string('mediaable_type', 100)->index();
            $table->string('type', 100)->index();
            $table->string('path', 512);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists(config('forumwise.db.prefix').'media');
    }
};
