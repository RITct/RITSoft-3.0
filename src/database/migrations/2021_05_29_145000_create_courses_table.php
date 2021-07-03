<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('semester');
            $table->enum('type', \App\Enums\CourseTypes::getValues());
            $table->boolean('active')->default(true);

            $table->string('subject_code', 6);
            $table->foreign('subject_code')
                ->references('code')
                ->on('subjects');


            $table->integer('classroom_id')->unsigned()->nullable();
            $table->foreign('classroom_id')
                ->references('id')
                ->on('classrooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
