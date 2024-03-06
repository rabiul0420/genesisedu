<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->float('full_mark')->default(0);
            $table->float('pass_mark_percent')->default(60);
            $table->unsignedTinyInteger('total_question')->default(0);
            $table->unsignedSmallInteger('duration');
            $table->unsignedTinyInteger('status')->default(1)->comment('0=Inactive, 1=Active, 2=Lock');
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
        Schema::dropIfExists('quiz_properties');
    }
}
