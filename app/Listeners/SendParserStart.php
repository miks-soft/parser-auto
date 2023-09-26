<?php

namespace App\Listeners;

use App\Events\ParserStartUpdate;
use App\Facades\APIService;

class SendParserStart
{

    /**
     * Handle the event.
     *
     * @param  \App\Events\ParserStartUpdate  $event
     * @return void
     */
    public function handle(ParserStartUpdate $event)
    {
       APIService::sendStart($event->source->value);
    }
}
