<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_number', 'status', 'ticket_id', 'bet_id', 'draw_id', 'user_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
