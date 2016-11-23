<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('owner_type')->nullable();
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->string('name')->nullable();
            $table->string('tag')->nullable();            
            $table->string('hash', 60)->nullable();
            $table->string('mime', 32)->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['disk', 'path']);
        });

        Schema::create('assets_usage', function (Blueprint $table) {
            $table->integer('asset_id')->unsigned();
            $table->morphs('item');

            $table->foreign('asset_id')->references('id')->on('assets_assets')
                    ->onUpdate('cascade')->onDelete('restrict');

            $table->primary(['asset_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets_assets');
        Schema::dropIfExists('assets_usage');
    }
}
