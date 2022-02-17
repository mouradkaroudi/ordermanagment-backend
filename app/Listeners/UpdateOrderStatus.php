<?php

namespace App\Listeners;

use App\Events\OrderSentToPurchase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateOrderStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderSentToPurchase  $event
     * @return void
     */
    public function handle(OrderSentToPurchase $event)
    {
        
        var_dump($event->order->update([
            'status' => 'issue'
        ]));
    }
}
