<?php

namespace App\Facades;

use App\Enums\SourcesEnum;
use Illuminate\Support\Facades\Facade;


/**
 * @package App/Facades
 * @method static float parse(string $from, string $to = 'CNY');
 */
class CurrencyService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CurrencyService';
    }
}
