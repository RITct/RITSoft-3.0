<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultyCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculty_course', function (Blueprint $table) {
            $table->id();

            $table->string("faculty_id")->unsigned()->nullable();
            $table->integer("course_id")->unsigned();

            $table->foreign("faculty_id")
                ->references("id")
                ->on("faculties")
                ->onDelete("set null");

            $table->foreign("course_id")
                ->references("id")
                ->on("courses");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faculty_course');
    }
}
