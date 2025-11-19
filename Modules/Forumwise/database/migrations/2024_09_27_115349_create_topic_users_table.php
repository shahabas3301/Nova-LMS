<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicUsersTable extends Migration
{
    public function up()
    {
        Schema::create(config('forumwise.db.prefix').'topic_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained(config('forumwise.db.prefix').'topics');
            $table->foreignId('user_id')->constrained(config('forumwise.user_table'));
            $table->tinyInteger('status')->nullable();
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('forumwise.db.prefix').'topic_users');
    }
}
