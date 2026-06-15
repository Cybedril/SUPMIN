<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mission_user', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('mission_user', function (Blueprint $table) {
            $table->dropColumn('submitted_at');
        });
    }
};