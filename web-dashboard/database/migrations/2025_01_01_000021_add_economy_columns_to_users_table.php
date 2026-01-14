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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_weekly')) {
                $table->bigInteger('last_weekly')->default(0)->after('last_daily');
            }
            if (!Schema::hasColumn('users', 'last_work')) {
                $table->bigInteger('last_work')->default(0)->after('last_weekly');
            }
            if (!Schema::hasColumn('users', 'coins')) {
                $table->bigInteger('coins')->default(0)->after('last_work');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_weekly', 'last_work']);
        });
    }
};
