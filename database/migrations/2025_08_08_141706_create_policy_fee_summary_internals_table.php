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
        Schema::create('policy_fee_summary_internals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->string('fee_provided_by')->nullable();
            $table->dateTime('date_fee_provided')->nullable();
            $table->string('controlling_person_fee_approved_by')->nullable();
            $table->dateTime('date_fee_approved')->nullable();
            $table->string('gii_fee_approved_by')->nullable();
            $table->dateTime('gii_date_fee_approved')->nullable();
            $table->text('fee_approval_notes')->nullable();
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
        Schema::dropIfExists('policy_fee_summary_internals');
    }
};
