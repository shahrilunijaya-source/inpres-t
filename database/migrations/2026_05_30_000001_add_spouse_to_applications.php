<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Civil marriage involves two parties. Store the spouse alongside the
 * applicant so the officer console can show both without a separate table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('spouse_name')->nullable()->after('applicant_name');
            $table->string('spouse_ic', 20)->nullable()->after('spouse_name');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['spouse_name', 'spouse_ic']);
        });
    }
};
