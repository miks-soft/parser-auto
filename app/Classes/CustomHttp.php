<?php

namespace App\Classes;
use App\Models\Proxy;
use Illuminate\Http\Client\Factory as BaseFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;

class CustomHttp extends BaseFactory
{
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }
        $retry = Config::get('project.HTTP_RETRY');
        $retryTimeout = Config::get('project.HTTP_RETRY_TIMEOUT');

        return tap($this->newPendingRequest(), function (PendingRequest $request) use ($retry, $retryTimeout) {
            $request->retry($retry, $retryTimeout * 1000, null, false);
            $headers = [
                'User-Agent'=>"'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 OPR/100.0.0.0'",
            ];
            $agent = ProxyHelper::getRandomUserAgent();
            if($agent)
            {
                $headers['User-Agent'] = $agent;
            }
            $request->withHeaders($headers);
            $request->stub($this->stubCallbacks)
                ->preventStrayRequests($this->preventStrayRequests);
        })->{$method}(...$parameters);
    }
}
