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
        Schema::table('hackathons', function (Blueprint $table) {
            $table->enum('status', ['draft', 'active', 'finalized'])->default('active')->after('banner');
            $table->foreignId('winner_group_id')->nullable()->constrained('grupos')->nullOnDelete()->after('status');
            $table->timestamp('finalized_at')->nullable()->after('winner_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathons', function (Blueprint $table) {
            $table->dropForeign(['winner_group_id']);
            $table->dropColumn(['status', 'winner_group_id', 'finalized_at']);
        });
    }
};
