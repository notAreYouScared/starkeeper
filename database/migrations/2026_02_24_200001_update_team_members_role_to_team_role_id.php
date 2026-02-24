<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->foreignId('team_role_id')->nullable()->after('member_id')->constrained('team_roles')->nullOnDelete();
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->enum('role', ['leader', 'member'])->default('member')->after('member_id');
            $table->dropForeign(['team_role_id']);
            $table->dropColumn('team_role_id');
        });
    }
};
