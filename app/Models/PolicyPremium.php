<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PolicyPremium extends Model
{
    use SoftDeletes;

    protected $table = 'policy_premiums';

    protected $guarded = [];
}
