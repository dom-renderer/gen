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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['individual', 'entity'])->default('individual');
            $table->string('name')->nullable();
            $table->string('controlling_person_name')->nullable();
            $table->string('email')->nullable();
            $table->string('dial_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('dob')->nullable();
            $table->text('place_of_birth')->nullable();
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->enum('status', ['single', 'married', 'divorced', 'separated', 'corporation', 'llc', 'trust', 'partnership', 'foundation', 'other'])->default('corporation');
            $table->string('status_name')->nullable()->comment('Applicable in other status');
            $table->string('national_country_of_registration')->nullable();
            $table->string('country_of_legal_residence')->nullable();

            $table->string('passport_number')->nullable();
            $table->string('country_of_issuance')->nullable();
            $table->string('tin')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
