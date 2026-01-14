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
        Schema::create('automations', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('name');
            $table->string('event'); // message_create, voice_join
            $table->text('trigger_value')->nullable(); // keyword, channel_id
            $table->string('action_type'); // reply, add_role, remove_role
            $table->text('action_value')->nullable(); // message content, role_id
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automations');
    }
};
