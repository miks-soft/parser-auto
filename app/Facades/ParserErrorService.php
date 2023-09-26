<?php

namespace App\Facades;

use App\Enums\SourcesEnum;
use App\Models\ParserLog;
use Illuminate\Support\Facades\Facade;


/**
 * @package App/Facades
 * @method static ParserLog store(SourcesEnum $enum, \Exception $exception);
 */
class ParserErrorService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ParserErrorService';
    }
}
