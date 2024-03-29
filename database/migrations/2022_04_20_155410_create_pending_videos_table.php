<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('priority')->default(0);
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('video_id');
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
        Schema::dropIfExists('pending_videos');
    }
}
