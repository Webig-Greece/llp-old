<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreatmentsTable extends Migration
{
    public function up()
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('user_id'); // Professional who created the treatment
            $table->string('template_type'); // BIRP or DAP
            $table->text('behavior_or_data')->nullable(); // Behavior (BIRP) or Data (DAP)
            $table->text('intervention_or_assessment')->nullable(); // Intervention (BIRP) or Assessment (DAP)
            $table->text('response')->nullable(); // Response (BIRP only)
            $table->text('plan'); // Plan (common to both templates)
            $table->date('start_date'); // Start date of the treatment
            $table->date('end_date')->nullable(); // End date of the treatment (if applicable)
            $table->string('status')->default('active'); // Status of the treatment (e.g., active, completed, canceled)
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patient_records')->onDelete('cascade'); // Corrected table name
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }



    public function down()
    {
        Schema::dropIfExists('treatments');
    }
}
