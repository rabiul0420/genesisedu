<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizPropertyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_property_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('quiz_property_id')->index();
            $table->unsignedTinyInteger('question_type');
            $table->unsignedTinyInteger('number_of_question')->default(0);
            $table->float('per_question_mark')->default(0);
            $table->float('per_question_negative_mark')->default(0);
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
        Schema::dropIfExists('quiz_property_items');
    }
}
