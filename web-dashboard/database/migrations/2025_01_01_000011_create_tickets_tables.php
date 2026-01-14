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
        // Settings for the guild (One per guild)
        Schema::create('ticket_configs', function (Blueprint $table) {
            $table->string('guild_id')->primary();
            $table->string('category_id')->nullable(); // Where tickets are created
            $table->string('support_role_id')->nullable(); // Who can see tickets
            $table->string('log_channel_id')->nullable(); // Where transcripts go
            $table->text('ticket_message')->nullable(); // Message inside ticket
            $table->string('ticket_embed_title')->nullable()->default('Support Ticket');
            $table->text('ticket_embed_description')->nullable();
            $table->string('ticket_embed_color')->nullable()->default('#6c63ff');
            $table->timestamps();
        });

        // Individual Tickets
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // For button IDs
            $table->string('guild_id')->index();
            $table->string('user_id');
            $table->string('channel_id')->unique();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_configs');
        Schema::dropIfExists('tickets');
    }
};
