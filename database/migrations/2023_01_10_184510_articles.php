<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Articles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_url');
            $table->string('source')->nullable();
            $table->string('app_category');
            $table->string('subreddit')->nullable();
            $table->string('created_utc')->nullable();
            $table->string('author')->nullable();
            $table->mediumText('url');
            $table->string('reddit_article_id')->nullable()->unique();
            
            $table->foreignId('app_id_fk')->references('id')->on('apps');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
