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
        Schema::create('giveaways', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('channel_id');
            $table->string('message_id')->nullable(); // Filled by bot
            $table->string('prize');
            $table->text('description')->nullable();
            $table->integer('winner_count')->default(1);
            $table->timestamp('end_at');
            $table->string('status')->default('active'); // active, ended
            $table->string('host_id')->nullable(); // User ID of creator
            $table->timestamps();
        });

        Schema::create('giveaway_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giveaway_id')->constrained('giveaways')->onDelete('cascade');
            $table->string('user_id');
            $table->string('user_name');
            $table->timestamps();
            
            $table->unique(['giveaway_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giveaway_participants');
        Schema::dropIfExists('giveaways');
    }
};
