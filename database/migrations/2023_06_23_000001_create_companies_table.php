<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vat_number')->unique();
            $table->date('vat_verification_date')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('billing_address')->nullable();
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->timestamp('subscription_expiry')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
