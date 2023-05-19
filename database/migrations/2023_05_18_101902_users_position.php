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
        Schema::create("users_position", function (Blueprint $table) {
            $table->foreignId("user_id")->primary()->constrained("users")->cascadeOnDelete();
            $table->enum("role", ["staff", "kepsek", "wakil_kepsek", "operator"]);
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
        Schema::dropIfExists("users_position");
    }
};
