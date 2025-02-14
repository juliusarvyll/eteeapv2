<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->foreign('applicant_id')->references('applicant_id')->on('personal_infos')->onDelete('cascade');
            
            $table->string('type'); // To differentiate between education types
            
            // Common Fields
            $table->string('school_name')->nullable();
            $table->string('address')->nullable();
            $table->integer('date_from')->nullable();
            $table->integer('date_to')->nullable();
            $table->boolean('has_diploma')->default(false);
            $table->string('diploma_file')->nullable();
            
            // High School Specific
            $table->string('school_type')->nullable(); // For high school types
            $table->boolean('is_senior_high')->default(false);
            $table->string('strand')->nullable();
            
            // PEPT Specific
            $table->integer('pept_year')->nullable();
            $table->string('pept_grade')->nullable();
            
            // Post Secondary Specific
            $table->string('program')->nullable();
            $table->string('institution')->nullable();
            $table->string('school_year')->nullable();
            
            // Non-Formal Specific
            $table->string('title')->nullable();
            $table->string('organization')->nullable();
            $table->string('certificate')->nullable();
            $table->string('participation')->nullable();
            
            // Certification Specific
            $table->string('agency')->nullable();
            $table->integer('date_certified')->nullable();
            $table->string('rating')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('education');
    }
}; 