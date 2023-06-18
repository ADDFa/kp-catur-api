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
            $table->string("number");
            $table->date("date");
            $table->string("type");
            $table->foreignId("letter_category_id")->constrained("letter_categories");
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
