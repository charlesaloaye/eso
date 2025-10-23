<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('guest_email');
            $table->string('guest_name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('country')->nullable();
            $table->text('street_address')->nullable();
            $table->string('building_type')->nullable();
            $table->string('town_city')->nullable();
            $table->string('state')->nullable();
            $table->text('order_notes')->nullable();
            $table->boolean('deliver_to_different_address')->default(false);
            $table->text('different_address')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discounted_amount', 10, 2)->default(0);
            $table->uuid('discount_id')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
