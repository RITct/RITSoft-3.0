<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->integer("course_id");
            $table->foreign("course_id")
                ->references("id")
                ->on("courses")
                ->onDelete("cascade");

            $table->string("faculty_id");
            $table->foreign("faculty_id")
                ->references("id")
                ->on("faculties")
                ->onDelete("cascade");

            $table->json("data");
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
}
