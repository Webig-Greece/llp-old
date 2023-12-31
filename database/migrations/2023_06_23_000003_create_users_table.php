<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('date_of_birth')->nullable();
            $table->string('vat_number')->unique()->nullable();
            $table->boolean('is_freelancer')->nullable();
            $table->boolean('subscribed_from_trial')->default(false);
            $table->enum('profession', ['psychologist', 'counselor', 'coach', 'psychiatrist'])->nullable();
            $table->enum('account_type', ['main', 'secretary', 'professional'])->default('main');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('language')->nullable();
            $table->enum('default_template', ['BIRP', 'DAP'])->default('BIRP');
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();

            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('branch_id')->nullable()->constrained();
            $table->foreignId('role_id')->nullable()->constrained();
            $table->foreignId('subscription_plan_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
