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
        Schema::create('policy_fee_summary_commission_splits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id')->nullable();
            $table->unsignedBigInteger('policy_fee_summary_internal_fee_id')->nullable();
            $table->unsignedBigInteger('policy_introducer_id')->nullable();
            $table->double('commission')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_fee_summary_commission_splits');
    }
};
