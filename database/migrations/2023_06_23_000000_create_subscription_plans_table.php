<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('currency', 3);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly']);
            $table->integer('trial_days')->default(0);
            $table->boolean('is_for_company')->default(false);
            $table->decimal('price_per_user', 8, 2)->nullable();
            $table->boolean('allows_additional_professional_accounts')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
}
