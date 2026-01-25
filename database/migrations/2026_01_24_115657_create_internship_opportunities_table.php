<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('internship_opportunities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');

            $table->foreignId('major_id')->nullable()->constrained('majors'); // إن بدكم تحديد تخصص
            $table->string('location_type')->default('onsite'); // onsite/remote/hybrid
            $table->string('city')->nullable();

            $table->unsignedInteger('capacity')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('status')->default('published'); // draft/published/closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_opportunities');
    }
};