<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'result', 'draw_date', 'draw_time', 'user_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];


    public function winners()
    {
        return $this->hasMany(Winner::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
