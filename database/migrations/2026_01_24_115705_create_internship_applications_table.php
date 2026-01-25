<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('internship_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('internship_opportunity_id')->constrained('internship_opportunities')->cascadeOnDelete();

            $table->text('motivation')->nullable();
            $table->string('status')->default('applied'); // applied/shortlisted/rejected/withdrawn
            $table->timestamp('applied_at')->useCurrent();

            $table->timestamps();

            $table->unique(
                ['student_id', 'internship_opportunity_id'],
                'uniq_student_opportunity'
            );        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_applications');
    }
};