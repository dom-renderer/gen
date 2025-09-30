<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($policy) {
            $nextId = \Illuminate\Support\Facades\DB::table('policies')->max('id') + 1;
            $policy->policy_number = 'CASE-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        });
    }

    public function introducers()
    {
        return $this->hasMany(PolicyIntroducer::class);
    }
    public function holders() {
        return $this->hasMany(PolicyHolder::class)->orderBy('created_at', 'DESC');
    }

    public function controllers() {
        return $this->hasMany(PolicyController::class);
    }

    public function insuredlives() {
        return $this->hasMany(PolicyInsuredLifeInformation::class);
    }

    public function beneficiaries() {
        return $this->hasMany(PolicyBeneficiary::class);
    }

    public function documents() {
        return $this->hasMany(PolicyDocument::class);
    }

    public function economicprofile() {
        return $this->hasMany(PolicyEconomicProfile::class);
    }

    public function premium() {
        return $this->hasMany(PolicyPremium::class);
    }

    public function internalfeesummary() {
        return $this->hasMany(PolicyFeeSummaryInternalFee::class);
    }

    public function externalfeesummary() {
        return $this->hasMany(PolicyFeeSummaryExternal::class);
    }

    public function inception() {
        return $this->hasMany(PolicyInception::class);
    }

    public function ongoing() {
        return $this->hasMany(PolicyOngoing::class);
    }

    public function investmentNotes() {
        return $this->hasMany(PolicyInvestmentNote::class);
    }

    public function communication() {
        return $this->hasMany(PolicyCommunication::class);
    }

    public function casefilenotes() {
        return $this->hasMany(PolicyCaseFileNote::class);
    }

    public function managers() {
        return $this->hasMany(PolicyManager::class);
    }

    public function liklihoodr() {
        return $this->belongsTo(Liklihood::class);
    }

    public function investmentadv() {
        return $this->belongsTo(InvestmentAdvisor::class);
    }

    public function idfrole() {
        return $this->belongsTo(InvestmentDedicatedFund::class);
    }

    public function idfs() {
        return $this->hasMany(InvestmentDedicatedFund::class)->orderBy('created_at', 'DESC');
    }

    public function custodians() {
        return $this->belongsTo(Custodian::class);
    }
}
