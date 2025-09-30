<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyFeeSummaryCommissionSplit extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function policy() {
        return $this->belongsTo(Policy::class);
    }

    public function fsi() {
        return $this->belongsTo(PolicyFeeSummaryInternalFee::class);
    }

    public function introducer() {
        return $this->belongsTo(PolicyIntroducer::class);
    }
}
