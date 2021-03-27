<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Ticket;

class TicketCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param $ticket
     * @return void
     */

    public $ticket;
    public $user_id;

    public function __construct(Ticket $ticket, $user)
    {
        $this->ticket = $ticket;        
        $this->user_id = $user->id;        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('newTicket.988');
        // return new PrivateChannel('transaction.' . $this->ticket->transaction->id);
        return new PrivateChannel('user.' . $this->user_id);
    }

    // public function broadcastWith()
    // {
    //     return [
    //         'id' => $this->ticket->id,
    //         'total_amount' => $this->ticket->total_amount,
    //         'created_at' => $this->ticket->created_at->toFormattedDateString(),
    //         'user' => [
    //             'id' => $this->ticket->transaction->user->id,
    //             'username' => $this->ticket->transaction->user->username,
    //         ]
    //     ];
    // }

}
