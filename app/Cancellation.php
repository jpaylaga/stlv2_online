<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason', 'user_id', 'ticket_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
