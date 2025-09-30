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
        Schema::create('policy_key_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->enum('type', ['policy-holder', 'insured-life', 'beneficiary', 'investment-advisor', 'idf-name', 'idf-manager', 'custodian-bank'])->default('policy-holder');
            $table->enum('entity_type', ['Individual', 'Corporate', 'Trust', 'Foundation'])->default('Corporate');
            $table->string('name')->nullable();
            $table->tinyInteger('applicable')->default(false);
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('silent_save')->default(1);
            $table->boolean('in_draft')->default(0);
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
        Schema::dropIfExists('policy_key_roles');
    }
};
