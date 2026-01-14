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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('inviter_id');
            $table->string('invited_id');
            $table->string('code');
            $table->boolean('is_valid')->default(true);
            $table->boolean('is_fake')->default(false);
            $table->boolean('is_left')->default(false);
            $table->timestamps();

            $table->unique(['guild_id', 'invited_id']); // One invite record per user per guild
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
