<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PolicyIntroducerContactPerson extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
