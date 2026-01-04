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
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('allow_resubmission')->default(false)->after('is_active');
            $table->integer('max_file_size')->default(10240)->after('allow_resubmission'); // KB
            $table->string('allowed_file_types')->default('pdf,docx,doc,zip,jpg,jpeg,png')->after('max_file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['allow_resubmission', 'max_file_size', 'allowed_file_types']);
        });
    }
};
