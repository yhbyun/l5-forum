<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('github_id')->index();
            $table->string('github_name')->index();
            $table->string('github_url');
            $table->string('real_name')->nullable();
            $table->string('name')->nullable()->index();
            $table->string('email')->unique();
            $table->boolean('is_banned')->default(false)->index();
            $table->string('image_url')->nullable();
            $table->integer('topic_count')->default(0)->index();
            $table->integer('reply_count')->default(0)->index();
            $table->string('twitter_account')->nullable();
            $table->string('personal_website')->nullable();
            $table->string('signature')->nullable();
            $table->string('introduction')->nullable();
            $table->integer('notification_count')->default(0);
            $table->string('avatar');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
