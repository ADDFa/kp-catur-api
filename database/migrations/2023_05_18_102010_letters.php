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
        Schema::create("letters", function (Blueprint $table) {
            $table->id();
            $table->string("reference_number");
            $table->date("date");
            $table->string("letter_type");
            $table->enum("category", ["penting", "mendesak", "biasa"]);
            $table->string("regarding");
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
        Schema::dropIfExists("letters");
    }
};
