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
            $table->string('admission_id',15)->unique();
            $table->string('name',40);
            $table->string('phone', 13);
            $table->string('address', 256);
            $table->tinyInteger('current_sem');
            $table->integer('sem_reg_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->integer('curriculum_id')->unsigned();
            $table->integer('attendance_id')->unsigned();
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
