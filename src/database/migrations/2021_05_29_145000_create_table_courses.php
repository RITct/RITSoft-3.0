<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('semester');

            # Faculty is null only when deleted
            $table->string('faculty_id', 20)->nullable();
            $table->foreign('faculty_id')
                ->on('faculties')
                ->references('faculty_id')
                ->onDelete('set null');

            $table->string('subject_code', 6);
            $table->foreign('subject_code')
                ->on('subjects')
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
        Schema::dropIfExists('courses');
    }
}
