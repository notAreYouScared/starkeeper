<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_training_ratings', function (Blueprint $table) {
            $table->string('note', 300)->nullable()->after('rating');
            $table->foreignId('note_author_id')->nullable()->constrained('users')->nullOnDelete()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('member_training_ratings', function (Blueprint $table) {
            $table->dropForeign(['note_author_id']);
            $table->dropColumn(['note', 'note_author_id']);
        });
    }
};
