<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("dispositions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("incoming_letter_id")->constrained("incoming_letters", "letter_id");
            $table->foreignId("to")->constrained("users_position", "user_id");
            $table->text("message");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("dispositions");
    }
};
