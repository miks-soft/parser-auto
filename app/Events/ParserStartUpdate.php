<?php

namespace App\Events;

use App\Enums\SourcesEnum;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParserStartUpdate
{
    use Dispatchable, SerializesModels;

    public SourcesEnum $source;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SourcesEnum $source)
    {
        $this->source = $source;
    }
}
