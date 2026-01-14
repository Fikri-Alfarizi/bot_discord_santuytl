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
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('channel_id');
            $table->string('message_id')->index(); // The Poll Embed Message ID
            $table->string('user_id');
            $table->integer('option_index'); // 0, 1, 2, 3, 4
            $table->timestamps();

            // One vote per user per poll
            $table->unique(['message_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
