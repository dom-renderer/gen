<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Model
{
    use SoftDeletes, HasRoles;

    protected $guarded = [];

    protected $casts = [
        'dob' => 'date',
    ];
    public function getDobAttribute($value)
    {
        return \Illuminate\Support\Carbon::parse($value)->format('Y-m-d');
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getFullPhoneAttribute()
    {
        return $this->dial_code . ' ' . $this->phone_number;
    }

    public function getUserProfileAttribute() {
        if (!empty(trim($this->profile)) && file_exists(public_path("storage/customers/profile/{$this->profile}"))) {
            return asset("storage/customers/profile/{$this->profile}");
        }

        return asset('assets/images/profile.png');
    }

    public function holders () {
        return $this->belongsTo(PolicyHolder::class, 'holder_id');
    }
}
