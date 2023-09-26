<?php

namespace App\Classes;

use App\Models\Proxy;
use App\Models\UserAgent;
use Illuminate\Support\Carbon;

class ProxyHelper
{
    public static function getRandomProxy(): string | null
    {
        $proxy =  Proxy::query()
            ->orderBy('last_used', 'asc')
            ->first();

        if($proxy)
        {
            $proxy->update([
                'last_used'=>Carbon::now(),
            ]);
            return $proxy->address;
        }
        return null;
    }

    public static function getRandomUserAgent(): string | null
    {
        $agent = UserAgent::query()
            ->orderBy('last_used', 'asc')
            ->first();
        if($agent)
        {
            $agent->update([
                'last_used'=>Carbon::now(),
            ]);
        }
        return null;
    }
}
