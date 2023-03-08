<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArticleValuesToFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->string('title');
            $table->string('image_url');
            $table->string('source');
            $table->string('category');
            $table->string('published_at');
            $table->string('author');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('image_url');
            $table->dropColumn('source');
            $table->dropColumn('category');
            $table->dropColumn('published_at');
            $table->dropColumn('author');
        });
    }
}
