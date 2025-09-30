<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PolicyFeeSummaryInternal extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function items() {
        return $this->hasMany(PolicyFeeSummaryInternalFee::class);
    }

    public function adminSteps() {
        return $this->hasMany(PolicyFeeSummaryInternalAdminStep::class);
    }
}
