<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionReferenceBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_reference_books', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('reference_book_id')->nullable();
            $table->integer('page_no')->nullable();
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
        Schema::dropIfExists('question_reference_books');
    }
}
