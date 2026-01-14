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
        Schema::create('economy_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index();
            $table->string('type'); // daily, work, weekly, transfer, game_win, etc.
            $table->integer('amount');
            $table->string('description')->nullable(); // "Transfer to @user", "Daily Reward"
            $table->string('related_user_id')->nullable(); // For transfers
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economy_transactions');
    }
};
