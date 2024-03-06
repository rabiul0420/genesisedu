<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchScheduleMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up( )
    {
        Schema::create('batches_schedules_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('schedule_id' );
            $table->string('key');
            $table->text('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batches_schedules_meta', function (Blueprint $table) {
            //
        });
    }
}
