<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCurriculums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->float('series_marks_1')->nullable();
            $table->float('series_marks_2')->nullable();
            $table->float('university_marks')->nullable();
            $table->float('sessional_marks')->nullable();

            $table->string('student_admission_id');
            $table->foreign('student_admission_id')
                ->on('students')
                ->references('admission_id');

            $table->integer('course_id');
            $table->foreign('course_id')
                ->on('courses')
                ->references('id');

            $table->boolean('is_feedback_complete')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curriculums');
    }
}
