<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLectureVideoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if( !Schema::hasTable('lecture_video_items') ) {
            Schema::create('lecture_video_items', function (Blueprint $table) {
                $table->increments('id');
                $table->string('lecture_video_id');
                $table->string('title')->nullable()->default(NULL);
                $table->string('link')->nullable()->default(NULL);
                $table->string('password')->nullable()->default(NULL);
                $table->unsignedTinyInteger('status')->nullable()->comment( '1=active,0=inactive' )->default(1);
                $table->timestamps();
                $table->unsignedMediumInteger('created_by')->nullable()->default(NULL);
                $table->unsignedMediumInteger('updated_by')->nullable()->default(NULL);
                $table->softDeletes();
                $table->unsignedMediumInteger('deleted_by')->nullable()->default(NULL);
            });


            $items_insertaion_query = "INSERT INTO lecture_video_items( `lecture_video_id`, `title`, `link`, `password`, `status`, created_at, created_by, updated_at, updated_by, deleted_at, deleted_by ) ";
            $items_insertaion_query .= "SELECT `id`, `name`, `lecture_address`,`password`, `status`, created_at, created_by, updated_at, updated_by, deleted_at, deleted_by ";
            $items_insertaion_query .= "FROM lecture_video ";
            $items_insertaion_query .= "WHERE NOT EXISTS ( SELECT * FROM lecture_video_items WHERE lecture_video_id = lecture_video.id ) ";

            DB::statement( $items_insertaion_query );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lecture_video_items');
    }
}
