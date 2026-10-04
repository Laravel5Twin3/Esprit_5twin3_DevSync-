<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpResponse extends Model
{
    //
    protected $fillable = [
        'help_request_id',
        'user_id',
        'message',
        'status',
    ];

    public function request()
    {
        return $this->belongsTo(HelpRequest::class, 'help_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
