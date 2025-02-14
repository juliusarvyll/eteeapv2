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
        Schema::create('creative_works', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('significance')->nullable();
            $table->date('date_completed')->nullable();
            $table->string('corroborating_body')->nullable();
            $table->timestamps();

            $table->foreign('applicant_id')
                  ->references('applicant_id')
                  ->on('personal_infos')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creative_works');
    }
};
