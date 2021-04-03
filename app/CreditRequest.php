<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CreditHistory;

class CreditRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ref',
        'type', 
        'status', 
        'outlet',
        'amount',
        'description', 
        'user_id', 
        'player_id', 
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function updateUserCredit($withdrawConfirmation = false){

        $incharge = auth()->user();
        $player = $this->player;
        $amount = $this->amount;
        $type = $this->type;

        CreditHistory::create([
            'type' => in_array( $incharge->role, ['coordinator', 'area_admin'] ) ? 'transfer' : $type,
            'to' => $player->id,
            'from' => $incharge->id,
            'amount' => $amount,
            'user_id' => $incharge->id,
            'description' => ( $withdrawConfirmation && $this->status == 'cancelled') ? 'Refund to cancelled withdrawal.' : 'Player deposits.'
        ]);

        return true;
    }

}
