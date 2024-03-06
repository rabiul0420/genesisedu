<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLectureVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_lecture_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('doctor_course_id');
            $table->unsignedBigInteger('pending_lecture_id');
            $table->unsignedTinyInteger('status')->default(0)->comment('0=Request, 1=Link Upload, 2=Click, 3=Complete');
            $table->timestamp('end')->nullable();
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
        Schema::dropIfExists('request_lecture_videos');
    }
}
