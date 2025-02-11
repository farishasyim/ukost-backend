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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string("invoice");
            $table->foreignId("pivot_room_id")->references("id")->on("pivot_rooms");
            $table->foreignId("admin_id")->references("id")->on("users");
            $table->timestamp("date")->nullable();
            $table->enum("status", ["unpaid", "paid"])->default("unpaid");
            $table->double("price");
            $table->string("proof_payment")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
