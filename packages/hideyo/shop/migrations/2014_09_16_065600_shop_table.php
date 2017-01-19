<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShopTable extends Migration
{

    /**
     * Make changes to the table.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active')->default(false);
            $table->boolean('wholesale')->default(false);
            $table->string('title')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('slug');
            $table->string('logo_file_name')->nullable();
            $table->string('logo_file_path')->nullable();
            $table->string('background_color')->nullable();
            $table->string('secret_key')->nullable();
            $table->integer('language_id')->unsigned()->nullable();
            $table->timestamps();

            $table->string('email')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->longText('square_thumbnail_sizes')->nullable();
            $table->longText('widescreen_thumbnail_sizes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
