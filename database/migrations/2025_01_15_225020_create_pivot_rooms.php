<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pivot_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("customer_id")->references("id")->on("users");
            $table->foreignId("room_id")->references("id")->on("rooms");
            $table->timestamp("left_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_rooms');
    }
};
