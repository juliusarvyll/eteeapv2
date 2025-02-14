<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('learning_objectives', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id');
            $table->foreign('applicant_id')->references('applicant_id')->on('personal_infos')->onDelete('cascade');
            $table->string('firstPriority');
            $table->string('secondPriority')->nullable();
            $table->string('thirdPriority')->nullable();
            $table->text('goalStatement');
            $table->text('timeCommitment');
            $table->text('overseasPlan')->nullable();
            $table->string('costPayment');
            $table->string('otherCostPayment')->nullable();
            $table->string('completionTimeline');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('learning_objectives');
    }
}; 