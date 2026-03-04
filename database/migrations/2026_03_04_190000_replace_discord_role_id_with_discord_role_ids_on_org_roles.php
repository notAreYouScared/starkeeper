<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('org_roles', function (Blueprint $table) {
            $table->json('discord_role_ids')->nullable()->after('label');
        });

        // Migrate any existing single-ID values into the new JSON array column
        DB::table('org_roles')
            ->whereNotNull('discord_role_id')
            ->where('discord_role_id', '!=', '')
            ->eachById(function ($row) {
                DB::table('org_roles')
                    ->where('id', $row->id)
                    ->update(['discord_role_ids' => json_encode([$row->discord_role_id])]);
            });

        Schema::table('org_roles', function (Blueprint $table) {
            $table->dropUnique('org_roles_discord_role_id_unique');
            $table->dropColumn('discord_role_id');
        });
    }

    public function down(): void
    {
        Schema::table('org_roles', function (Blueprint $table) {
            $table->string('discord_role_id')->nullable()->unique()->after('label');
        });

        // Restore first element of the array back into the single-ID column
        DB::table('org_roles')
            ->whereNotNull('discord_role_ids')
            ->eachById(function ($row) {
                $ids = json_decode($row->discord_role_ids, true);
                if (! empty($ids)) {
                    DB::table('org_roles')
                        ->where('id', $row->id)
                        ->update(['discord_role_id' => $ids[0]]);
                }
            });

        Schema::table('org_roles', function (Blueprint $table) {
            $table->dropColumn('discord_role_ids');
        });
    }
};
