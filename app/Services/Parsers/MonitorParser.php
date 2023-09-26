<?php

namespace App\Services\Parsers;

use App\Enums\SourcesEnum;
use App\Facades\ParserProcessService;
use App\Models\ParserProcess;

class MonitorParser
{
    private int $carsPublished = 0;
    private ParserProcess $process;
    protected SourcesEnum $source;

    public function startMonitor()
    {
        $this->carsPublished = 0;
        $this->process = ParserProcessService::start($this->source);
    }

    public function stopMonitor(int $log_id = null)
    {
        $this->process = ParserProcessService::finish($this->process, $this->carsPublished, $log_id);
    }

    public function addCount(int $add = 1)
    {
        $this->carsPublished += $add;
        $this->process->update([
            'cars_count'=>$this->carsPublished,
        ]);
    }
}
