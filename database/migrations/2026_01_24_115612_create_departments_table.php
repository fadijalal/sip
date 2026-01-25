<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['university_id', 'name']); // لمنع تكرار اسم قسم داخل نفس الجامعة
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};