<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('admission_id', 15);
            $table->primary('admission_id');

            $table->string('name', 50);
            $table->string('address', 200);
            $table->string('phone', 13);
            $table->smallInteger('current_semester');
            $table->integer('roll_no')->nullable()->default(null);

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('department_code');
            $table->foreign('department_code')
                ->references('code')
                ->on('departments')
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
        Schema::dropIfExists('students');
    }
}
