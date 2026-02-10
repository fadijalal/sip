<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Schema::create('internship_assignments', function (Blueprint $table) {
            // $table->id();

            // $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            // $table->foreignId('internship_opportunity_id')->constrained('internship_opportunities')->cascadeOnDelete();

            // // اعتماد المشرف (اختياري لحين الموافقة)
            // $table->foreignId('approved_by_supervisor_id')->nullable()->constrained('supervisors')->nullOnDelete();

            // $table->string('assignment_status')->default('pending_approval');
            // // pending_approval/active/completed/cancelled

            // $table->date('start_date')->nullable();
            // $table->date('end_date')->nullable();

            // $table->timestamps();
        // });
    }

    public function down(): void
    {
        // Schema::dropIfExists('internship_assignments');
    }
};