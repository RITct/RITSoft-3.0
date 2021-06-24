<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\RequestTypes;
use \App\Enums\RequestStates;

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
            $table->integer("current_position")->default(1);
            $table->json("payload");
            $table->string("table_name");
            $table->string("primary_key");
            $table->enum("type", RequestTypes::getValues());
            $table->enum("state", RequestStates::getValues())->default(RequestStates::PENDING);
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
                ->on("users")
                ->onDelete("cascade");

            $table->enum("state", RequestStates::getValues())->default(RequestStates::PENDING);
            $table->smallInteger("position");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_signees');
        Schema::dropIfExists('requests');
    }
}
