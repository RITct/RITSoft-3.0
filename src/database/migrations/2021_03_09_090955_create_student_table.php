<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_id')->unique();
            $table->tinyInteger('current_sem');
            $table->integer('sem_id')->unsigned()->nullable();
            $table->integer('subject_id')->unsigned()->nullable();
            $table->integer('series_marks_id')->unsigned()->nullable();
            $table->integer('sessional_marks_id')->unsigned()->nullable();
            $table->integer('university_marks_id')->unsigned()->nullable();
            $table->integer('attendence_id')->unsigned()->nullable();
            $table->integer('feedback_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
