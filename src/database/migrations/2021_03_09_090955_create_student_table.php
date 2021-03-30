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
            $table->foreignId("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->string('admission_id',15)->unique();
            $table->string('name',40);
            $table->string('phone', 13);
            $table->string('address', 256);
            $table->tinyInteger('current_sem');
            $table->foreignId('sem_reg_id')->nullable();
            /*
            $table->integer('subject_id')->unsigned();
            $table->foreignId('curriculum_id');
            $table->integer('attendance_id')->unsigned();
            */
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
