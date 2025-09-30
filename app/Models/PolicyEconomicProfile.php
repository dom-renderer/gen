<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PolicyEconomicProfile extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'purpose_of_policy_and_structure' => 'array'
    ];
}
