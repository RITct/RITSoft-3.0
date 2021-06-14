<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClassrooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->enum("degree_type", \App\Enums\Degrees::getValues());
            $table->smallInteger("semester");
            $table->string("department_code");
            $table->foreign("department_code")
                ->references("code")
                ->on("departments")
                ->onDelete("cascade");

            $table->integer("promotion_id")->unsigned()->nullable();

            $table->timestamps();
        });

        Schema::table('classrooms', function (Blueprint $table){
            $table->foreign("promotion_id")
                ->references("id")
                ->on("classrooms");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classrooms');
    }
}
