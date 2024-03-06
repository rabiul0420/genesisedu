<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('quiz_property_id')->index();
            $table->unsignedInteger('batch_id')->index()->nullable();
            $table->string('title');
            $table->unsignedTinyInteger('status')->default(0)->comment('0=Inactive, 1=Active, 2=Lock');
            $table->string('answer_script')->nullable();
            $table->string('participant_link')->nullable();
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
        Schema::dropIfExists('quizzes');
    }
}
