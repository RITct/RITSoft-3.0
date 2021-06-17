<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->string('id', 20);
            $table->primary('id');

            $table->string('name', 50);
            $table->string('address', 200);
            $table->string('phone', 13);

            $table->integer('user_id');
            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');

            $table->string('department_code');
            $table->foreign('department_code')
                ->on('departments')
                ->references('code');

            $table->integer('advisor_classroom_id')->unsigned()->nullable();
            $table->foreign('advisor_classroom_id')
                ->references('id')
                ->on('classrooms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faculties');
    }
}
