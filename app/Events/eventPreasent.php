<?php

namespace App\Events;

use App\GiftAct;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class eventPreasent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $giftAct;

    /**
     * Create a new event instance.
     *
     * @param \App\GiftAct $giftAct
     */
    public function __construct(GiftAct $giftAct)
    {
        //
        $this->giftAct = $giftAct;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('gifs.'.$this->giftAct->target_id);
    }

    public function broadcastWith()
    {
        $this->giftAct->load('fromContact');

        return ["giftAct" => $this->giftAct];
    }
}
