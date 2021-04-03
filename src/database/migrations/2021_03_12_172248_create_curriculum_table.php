<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurriculumTable extends Migration
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
            $table->integer('subject_id')->unsigned();
            $table->string('student_admission_id');
            $table->double('series_marks_1')->nullable();
            $table->double('series_marks_2')->nullable();
            $table->double('sessional_marks')->nullable();
            $table->double('university_marks')->nullable();
            $table->boolean('feedback');
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
        Schema::dropIfExists('curriculums');
    }
}
