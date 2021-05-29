<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAbsentees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absentees', function (Blueprint $table) {
            $table->id();
            $table->boolean('duty_leave')->default(false);
            $table->boolean('medical_leave')->default(false);

            $table->integer('attendance_id');
            $table->foreign('attendance_id')
                ->on('attendance')
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absentees');
    }
}