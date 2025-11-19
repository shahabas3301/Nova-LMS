<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create(config('forumwise.db.prefix').'likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('likeable_id')->index();
            $table->string('likeable_type', 100)->index();
            $table->tinyInteger('type')->default(0);
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists(config('forumwise.db.prefix').'likes');
    }
};
