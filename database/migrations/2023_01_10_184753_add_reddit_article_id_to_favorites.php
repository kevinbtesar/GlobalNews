<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedditArticleIdToFavorites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorites', function (Blueprint $table) {
            // $table->dropColumn('article_id');
            // $table->dropColumn('title');
            // $table->dropColumn('image_url');
            // $table->dropColumn('source');
            // $table->dropColumn('category');
            // $table->dropColumn('created_utc');
            // $table->dropColumn('author');
            // $table->dropColumn('url');

            // $table->foreignId('article_id_fk')->references('id')->on('reddit_articles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('favorites', function (Blueprint $table) {
        //     $table->dropColumn('unsignedBigInteger');
        // });
    }
}
