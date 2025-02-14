<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lifelong_learnings', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->enum('type', ['hobby', 'skill', 'work', 'volunteer', 'travel'])->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('applicant_id')
                  ->references('applicant_id')
                  ->on('personal_infos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lifelong_learnings');
    }
};