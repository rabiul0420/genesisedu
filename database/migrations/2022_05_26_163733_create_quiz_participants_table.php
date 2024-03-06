<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('doctor_id')->index();
            $table->unsignedBigInteger('quiz_id')->index();
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('quiz_answer_script')->nullable();
            $table->string('doctor_answer_script')->nullable();
            $table->unsignedTinyInteger('correct_answer')->default(0);
            $table->unsignedTinyInteger('wrong_answer')->default(0);
            $table->float('obtained_mark')->default(0);
            $table->string('coupon')->nullable();
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
        Schema::dropIfExists('quiz_participants');
    }
}
