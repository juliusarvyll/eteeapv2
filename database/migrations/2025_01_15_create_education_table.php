<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->foreign('applicant_id')->references('applicant_id')->on('personal_infos')->onDelete('cascade');

            // Education Type (Enum to limit values)
            $table->string('type'); // covers elementary, high school, and PEPT
            $table->string('strand')->nullable();
            // Common Fields
            $table->string('school_name')->nullable();
            $table->string('address')->nullable();
            $table->year('date_from')->nullable();
            $table->year('date_to')->nullable();
            $table->boolean('has_diploma')->default(false);
            $table->json('diploma_files')->nullable();

            $table->timestamps();

            // Indexing foreign key for performance
            $table->index('applicant_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('educations');
    }
};
