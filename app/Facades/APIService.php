<?php

namespace App\Facades;

use App\Enums\SourcesEnum;
use Illuminate\Support\Facades\Facade;


/**
 * @package App/Facades
 * @method static bool sendStart(string $source)
 * @method static bool sendStop(string $source)
 * @method static bool | \stdClass getModel(string $source_id)
 * @method static bool | \stdClass getOffer(SourcesEnum $source, string $sku_id, $show_hidden = true)
 * @method static bool updateOffer(string $id, array $data = [])
 * @method static \stdClass | bool createOffer(array $data, array $images = [])
 * @method static bool uploadRates(array $rates)
 */
class APIService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'APIService';
    }
}
