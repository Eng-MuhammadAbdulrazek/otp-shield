<?php

namespace OtpShield\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasUuids;

    protected $table = 'otps';

    protected $fillable = [
        'secret', 'type', 'digits', 'period', 'counter', 'attempts', 'last_used_at', 'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function otpable()
    {
        return $this->morphTo();
    }
}
