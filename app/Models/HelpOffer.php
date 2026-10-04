<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpOffer extends Model
{
    //
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'available_from',
        'available_until',
        'status',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
