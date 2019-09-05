<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_tag', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('tag_id');

            $table->unique(['board_id', 'tag_id']);

            $table->foreign('board_id')
                ->on('boards')
                ->references('id')
                ->onDelete('cascade');
            $table->foreign('tag_id')
                ->on('tags')
                ->references('id')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_tag');
    }
}
