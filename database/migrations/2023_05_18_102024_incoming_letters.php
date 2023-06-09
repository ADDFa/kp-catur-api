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
        Schema::create("incoming_letters", function (Blueprint $table) {
            $table->foreignId("letter_id")->primary()->constrained("letters")->cascadeOnDelete();
            $table->string("sender");
            $table->string("letter_image");
            $table->enum("disposition_status", ["process", "finish"])->nullable();
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
        Schema::dropIfExists("incoming_letters");
    }
};
