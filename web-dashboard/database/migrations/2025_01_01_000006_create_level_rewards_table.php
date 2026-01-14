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
        Schema::create('level_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id');
            $table->integer('level');
            $table->string('role_id');
            $table->timestamps();

            $table->index('guild_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_rewards');
    }
};
