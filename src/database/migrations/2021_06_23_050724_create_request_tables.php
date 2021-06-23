<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->integer("current_signee_id");
            $table->json("payload");
            $table->timestamps();
        });

        Schema::create('request_signees', function (Blueprint $table) {
            $table->id();
            $table->integer("request_id");
            $table->foreign("request_id")
                ->references("id")
                ->on("requests")
                ->onDelete("cascade");

            $table->integer("user_id");
            $table->foreign("user_id")
                ->references("id")
                ->on("requests")
                ->onDelete("cascade");

            $table->boolean("approved")->default(false);
            $table->smallInteger("position");
        });

        Schema::table('requests', function (Blueprint $table){
            $table->foreign("current_signee_id")
                ->references("id")
                ->on("request_signees");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
        Schema::dropIfExists('request_signees');
    }
}
