<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    //
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'needed_from',
        'needed_until',
        'status',
    ];

    protected $casts = [
        'needed_from' => 'datetime',
        'needed_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(HelpResponse::class);
    }
}
