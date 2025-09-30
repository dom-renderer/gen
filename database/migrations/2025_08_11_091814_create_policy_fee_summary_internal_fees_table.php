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
        Schema::create('policy_fee_summary_internal_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_fee_summary_internal_id');
            $table->string('type')->nullable();
            $table->string('frequency')->nullable();
            $table->double('amount')->default(0);
            $table->double('rate')->default(0);
            $table->string('fee_option')->nullable();
            $table->double('commission_split')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_fee_summary_internal_fees');
    }
};
