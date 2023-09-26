<?php

namespace App\Services;

use App\Enums\SourcesEnum;
use App\Models\ParserLog;

class ErrorLogService
{
    public function store(SourcesEnum $enum, \Exception $exception): ParserLog
    {
        return ParserLog::query()
            ->create([
                'source'=>$enum,
                'error' => $exception->getMessage(),
                'full' => $exception->getTraceAsString()
            ])
            ->refresh();
    }
}
