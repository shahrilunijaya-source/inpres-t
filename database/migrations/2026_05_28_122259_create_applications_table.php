<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 24)->unique();
            $table->enum('doc_type', ['birth', 'marriage', 'mykad']);
            $table->string('applicant_ic', 14);
            $table->string('applicant_name', 120);
            $table->string('applicant_address', 200);
            $table->enum('status', [
                'received',
                'verified',
                'officer_review',
                'approved',
                'issued',
                'rejected',
            ])->default('received');
            $table->decimal('ai_score', 4, 3)->nullable();
            $table->dateTime('ai_eta')->nullable();
            $table->enum('sla_state', ['on_track', 'at_risk', 'breached'])->default('on_track');
            $table->foreignId('assigned_officer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'sla_state']);
            $table->index('applicant_ic');
            $table->index('doc_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
