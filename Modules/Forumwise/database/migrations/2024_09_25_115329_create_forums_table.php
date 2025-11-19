<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumsTable extends Migration
{
    public function up()
    {
        Schema::create(config('forumwise.db.prefix').'forums', function (Blueprint $table) {
            $table->id();
            $table->string('title')->fullText();
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable()->fullText();
            $table->tinyInteger('status')->default(1);
            $table->json('topic_role');
            $table->foreignId('category_id')->constrained(config('forumwise.db.prefix').'categories')->onDelete('cascade');
            $table->foreignId('created_by')->constrained(config('forumwise.user_table'))->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('forumwise.db.prefix').'forums');
    }
}