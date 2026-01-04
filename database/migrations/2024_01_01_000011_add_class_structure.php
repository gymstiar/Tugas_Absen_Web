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
        // Add mentor_id to class_groups - defines which mentor owns this class
        Schema::table('class_groups', function (Blueprint $table) {
            $table->foreignId('mentor_id')->nullable()->after('description')->constrained('users')->nullOnDelete();
        });

        // Add class_group_id to users - defines which class this user belongs to
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('class_group_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_groups', function (Blueprint $table) {
            $table->dropForeign(['mentor_id']);
            $table->dropColumn('mentor_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_group_id']);
            $table->dropColumn('class_group_id');
        });
    }
};
