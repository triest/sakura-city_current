<?php

namespace App\Events;

use App\MyRequwest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class newApplication implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $requwest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MyRequwest $requwest)
    {
        $this->requwest=$requwest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('requwests.' . $this->requwest->target_id);
    }

    public function broadcastWith()
    {
        $this->requwest->load('fromContact');
        return ["requwest" => $this->requwest];
    }
}
