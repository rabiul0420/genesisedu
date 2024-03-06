<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('quiz_id')->index();
            $table->unsignedBigInteger('question_id')->index();
            $table->unsignedSmallInteger('serial')->nullable()->comment('null is unpinned');
            $table->unsignedTinyInteger('question_type');
            $table->text('question_title')->nullable();
            $table->json('question_options')->nullable();
            $table->string('answer_script')->nullable();
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
        Schema::dropIfExists('quiz_questions');
    }
}
