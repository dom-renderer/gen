<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PolicyManager extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function policy() {
        return $this->belongsTo(Policy::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'manager_id');        
    }
}