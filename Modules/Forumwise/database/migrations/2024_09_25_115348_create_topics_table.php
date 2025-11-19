<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    public function up()
    {
        Schema::create(config('forumwise.db.prefix').'topics', function (Blueprint $table) {
            $table->id();
            $table->string('title')->fullText();
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable()->fullText();
            $table->json('tags')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->foreignId('forum_id')->constrained(config('forumwise.db.prefix').'forums')->onDelete('cascade');
            $table->foreignId('created_by')->constrained(config('forumwise.user_table'))->onDelete('cascade');
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('forumwise.db.prefix').'topics');
    }
}
