<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("office_staff", function (Blueprint $table) {
            $table->string("id", 20)->primary();
            $table->string("name");
            $table->string("phone");
            $table->string("address")->nullable();
            $table->integer("user_id");
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_staff');
    }
}
