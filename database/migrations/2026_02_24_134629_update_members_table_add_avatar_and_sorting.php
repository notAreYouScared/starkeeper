<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('handle');
            $table->string('profile_url')->nullable()->after('avatar_url');
            $table->unsignedBigInteger('org_role_id')->nullable()->after('org_role');
            $table->unsignedInteger('sort_order')->default(0)->after('org_role_id');
        });

        // Migrate existing org_role enum values to org_role_id
        DB::table('members')->orderBy('id')->each(function ($member) {
            $role = DB::table('org_roles')->where('name', $member->org_role)->first();
            if ($role) {
                DB::table('members')->where('id', $member->id)->update(['org_role_id' => $role->id]);
            }
        });

        Schema::table('members', function (Blueprint $table) {
            $table->foreign('org_role_id')->references('id')->on('org_roles')->restrictOnDelete();
            $table->dropColumn('org_role');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->enum('org_role', ['leadership', 'director', 'mod', 'member'])->default('member')->after('title');
        });

        DB::table('members')->orderBy('id')->each(function ($member) {
            if ($member->org_role_id) {
                $role = DB::table('org_roles')->where('id', $member->org_role_id)->first();
                if ($role) {
                    DB::table('members')->where('id', $member->id)->update(['org_role' => $role->name]);
                }
            }
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['org_role_id']);
            $table->dropColumn(['org_role_id', 'sort_order', 'profile_url', 'avatar_url']);
        });
    }
};
