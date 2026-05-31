<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Deep LM01-style form payload (birth: child/mother/father/deliverer/
            // informant sections). Nullable — legacy + simple doc types leave it null.
            $table->json('form_data')->nullable()->after('applicant_address');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('form_data');
        });
    }
};
