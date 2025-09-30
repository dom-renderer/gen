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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number')->index();
            $table->dateTime('opening_date')->nullable();
            $table->unsignedBigInteger('liklihood')->nullable();
            $table->string('status')->default('DRAFT');
            $table->boolean('investment_advisor_manager_applicable')->default(true);
            $table->boolean('idf_name_applicable')->default(true);
            $table->boolean('idf_manager_applicable')->default(true);
            $table->boolean('custodian_applicable')->default(true);
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
        Schema::dropIfExists('policies');
    }
};
