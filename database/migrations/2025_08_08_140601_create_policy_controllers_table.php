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
        Schema::create('policy_controllers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id')->nullable();

            $table->boolean('primary')->default(false);

            $table->enum('entity_type', ['individual', 'corporate', 'trust', 'foundation'])->default('individual');
            $table->string('full_name')->nullable()->comment('Applicable for non-individual');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('dial_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->text('notes')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->enum('personal_status', ['single', 'married', 'divorced', 'separated', 'corporation', 'llc', 'trust', 'partnership', 'foundation', 'other'])->default('corporation');
            $table->string('personal_status_other')->nullable()->comment('Applicable in other selected personal status');
            $table->enum('smoker_status', ['smoker', 'non-smoker'])->nullable();
            $table->string('national_country_of_registration')->nullable();
            $table->string('country_of_legal_residence')->nullable();
            
            $table->string('relationship_to_policyholder')->nullabel();

            $table->json('countries_of_tax_residence')->nullable();
            $table->json('passport_number')->nullable();
            $table->json('country_of_issuance')->nullable();
            $table->json('tin')->nullable();
            $table->string('lei')->nullable();

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
        Schema::dropIfExists('policy_controllers');
    }
};
