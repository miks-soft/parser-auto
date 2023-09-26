<?php

namespace App\Facades;

use App\Enums\SourcesEnum;
use App\Models\ParserProcess;
use Illuminate\Support\Facades\Facade;


/**
 * @package App/Facades
 * @method static ParserProcess start(SourcesEnum $source)
 * @method static ParserProcess finish(ParserProcess $process, int $cars, int $log_id = null)
 * @method static void checkProcessState(ParserProcess $process)
 * @method static void stopProcess(ParserProcess $process)
 */
class ParserProcessService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ParserProcessService';
    }
}
