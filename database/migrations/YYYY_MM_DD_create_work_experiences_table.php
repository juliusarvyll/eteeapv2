<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->foreign('applicant_id')->references('applicant_id')->on('personal_infos')->onDelete('cascade');
            $table->enum('employment_type', ['employed', 'self_employed', 'no_employment'])->default('no_employment');
            $table->string('designation')->nullable();
            $table->integer('dateFrom')->nullable();
            $table->integer('dateTo')->nullable();
            $table->string('companyName')->nullable();
            $table->text('companyAddress')->nullable();
            $table->string('employmentStatus')->nullable();
            $table->string('supervisorName')->nullable();
            $table->text('reasonForLeaving')->nullable();
            $table->text('responsibilities')->nullable();
            $table->string('documents')->nullable();
            $table->string('reference1_name')->nullable();
            $table->string('reference1_contact')->nullable();
            $table->string('reference2_name')->nullable();
            $table->string('reference2_contact')->nullable();
            $table->string('reference3_name')->nullable();
            $table->string('reference3_contact')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_experiences');
    }
}; 