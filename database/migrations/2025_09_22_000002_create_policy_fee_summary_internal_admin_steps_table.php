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
		Schema::create('policy_fee_summary_internal_admin_steps', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('policy_fee_summary_internal_id');
			$table->integer('position')->default(0);
			$table->double('from_value')->nullable();
			$table->double('to_value')->nullable();
			$table->double('rate_or_amount')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('policy_fee_summary_internal_admin_steps');
	}
};


