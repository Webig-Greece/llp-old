<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('user_id');
            $table->string('patient_first_name');
            $table->string('patient_last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->dateTime('next_appointment')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('gender')->nullable();
            $table->string('insurance_information')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_records');
    }
}
