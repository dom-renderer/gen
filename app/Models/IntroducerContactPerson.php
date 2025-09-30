<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntroducerContactPerson extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function introducer()
    {
        return $this->belongsTo(Introducer::class);
    }
}


