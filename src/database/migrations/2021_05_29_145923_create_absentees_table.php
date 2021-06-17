<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\LeaveType;

class CreateAbsenteesTable extends Migration
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
            $table->enum("leave_excuse", LeaveType::getValues())
                ->default(LeaveType::NO_EXCUSE);

            $table->integer('attendance_id');
            $table->foreign('attendance_id')
                ->on('attendance')
                ->references('id')
                ->onDelete('cascade');

            $table->string('student_admission_id', 15);
            $table->foreign('student_admission_id')
                ->on('students')
                ->references('admission_id');
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
