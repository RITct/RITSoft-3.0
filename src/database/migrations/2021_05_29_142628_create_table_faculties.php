<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFaculties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->string('faculty_id', 20);
            $table->primary('faculty_id');

            $table->string('name', 50);
            $table->string('address', 200);
            $table->string('phone', 13);

            $table->boolean('is_hod')->default(false);
            $table->integer('user_id');
            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');

            $table->string('department_code');
            $table->foreign('department_code')
                ->on('departments')
                ->references('code');
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
