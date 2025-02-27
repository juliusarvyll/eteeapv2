<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Post Secondary Specific Fields
        Schema::create('education_post_secondary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('education_id');
            $table->foreign('education_id')->references('id')->on('educations')->onDelete('cascade');

            $table->string('program')->nullable();
            $table->string('institution')->nullable();
            $table->string('school_year')->nullable();
            $table->json('diploma_files')->nullable();

            $table->timestamps();
        });

        // Non-Formal Specific Fields
        Schema::create('education_non_formal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('education_id');
            $table->foreign('education_id')->references('id')->on('educations')->onDelete('cascade');

            $table->string('title')->nullable();
            $table->string('organization')->nullable();
            $table->json('certificate_files')->nullable();
            $table->string('participation')->nullable();

            $table->timestamps();
        });

        // Certification Specific Fields
        Schema::create('education_certification', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('education_id');
            $table->foreign('education_id')->references('id')->on('educations')->onDelete('cascade');

            $table->string('agency')->nullable();
            $table->year('date_certified')->nullable();
            $table->string('rating')->nullable();
            $table->json('certificate_files')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('education_post_secondary');
        Schema::dropIfExists('education_non_formal');
        Schema::dropIfExists('education_certification');
    }
};
