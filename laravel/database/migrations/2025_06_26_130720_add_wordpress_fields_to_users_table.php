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
            $table->unsignedBigInteger('wordpress_id')->nullable()->after('id');
            $table->string('wordpress_username')->nullable()->after('wordpress_id');
            $table->index('wordpress_id');
            $table->index('wordpress_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['wordpress_id']);
            $table->dropIndex(['wordpress_username']);
            $table->dropColumn(['wordpress_id', 'wordpress_username']);
        });
    }
}; 