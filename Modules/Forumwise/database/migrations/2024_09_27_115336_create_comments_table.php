<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create(config('forumwise.db.prefix').'comments', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->foreignId('topic_id')->constrained(config('forumwise.db.prefix').'topics')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained(config('forumwise.db.prefix').'comments')->onDelete('cascade');
            $table->foreignId('created_by')->constrained(config('forumwise.user_table'))->onDelete('cascade');
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('forumwise.db.prefix').'comments');
    }
}

