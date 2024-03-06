<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('from_doctor_course_id')->index();
            $table->unsignedBigInteger('to_doctor_course_id')->index()->nullable();
            $table->float('shift_fee')->default(0);
            $table->float('service_charge')->default(0);
            $table->float('payment_adjustment')->default(0);
            $table->string('note')->nullable();
            $table->timestamp('shifted_at')->nullable();
            $table->unsignedBigInteger('shifted_by')->index();
            $table->json('histories')->nullable();
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
        Schema::dropIfExists('batch_shifts');
    }
}
