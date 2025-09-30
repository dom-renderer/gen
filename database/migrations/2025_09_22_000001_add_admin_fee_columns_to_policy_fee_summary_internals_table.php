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
		Schema::table('policy_fee_summary_internals', function (Blueprint $table) {
			$table->string('admin_fee_type')->nullable()->after('fee_approval_notes');
			$table->string('admin_fee_applied_to')->nullable()->after('admin_fee_type');
			$table->string('admin_fee_limit')->nullable()->after('admin_fee_applied_to');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('policy_fee_summary_internals', function (Blueprint $table) {
			$table->dropColumn(['admin_fee_type', 'admin_fee_applied_to', 'admin_fee_limit']);
		});
	}
};


