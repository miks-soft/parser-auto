<?php

namespace App\Listeners;

use App\Events\ParserStopUpdate;
use App\Facades\APIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendParserStop
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ParserStopUpdate  $event
     * @return void
     */
    public function handle(ParserStopUpdate $event)
    {
        APIService::sendStop($event->source->value);
    }
}
