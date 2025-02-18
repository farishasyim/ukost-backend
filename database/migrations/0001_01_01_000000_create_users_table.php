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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password')->default('$2y$12$R21X/OxAZ1gdLXf5uX.bwOlWtmN5kM8eiMChlSgyPfHR9Yj6ctjdm');
            $table->enum("role", ["customer", "admin"]);
            $table->bigInteger("identity_number")->nullable();
            $table->string("identity_card")->nullable();
            $table->string("profile_picture")->nullable();
            $table->enum("gender", ["laki-laki", "perempuan"]);
            $table->string("phone");
            $table->date("date_of_birth")->nullable();
            $table->timestamp("deleted_at")->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
