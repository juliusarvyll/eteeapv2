<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_awards', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->string('title')->nullable();
            $table->string('institution')->nullable();
            $table->date('dateReceived')->nullable();
            $table->text('description')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
            
            $table->foreign('applicant_id')
                  ->references('applicant_id')
                  ->on('personal_infos')
                  ->onDelete('cascade');
        });

        Schema::create('community_awards', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->string('title')->nullable();
            $table->string('organization')->nullable();
            $table->date('dateAwarded')->nullable();
            $table->timestamps();
            
            $table->foreign('applicant_id')
                  ->references('applicant_id')
                  ->on('personal_infos')
                  ->onDelete('cascade');
        });

        Schema::create('work_awards', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->string('title')->nullable();
            $table->string('organization')->nullable();
            $table->date('dateAwarded')->nullable();
            $table->timestamps();
            

            $table->foreign('applicant_id')
                  ->references('applicant_id')
                  ->on('personal_infos')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_awards');
        Schema::dropIfExists('community_awards');
        Schema::dropIfExists('academic_awards');
    }
}; 