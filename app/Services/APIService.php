<?php

namespace App\Services;

use App\Enums\SourcesEnum;
use App\Enums\StorageEnum;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class APIService
{
    protected string $host;
    private string $auth;
    private string $endpointStartParse;
    private string $endpointStopParse;
    private string $endpointModel;
    private string $endpointOffer;
    private string $endpointConfig;

    public function __construct()
    {
        $this->host = Config::get('project.API_HOST');
        $this->auth = Config::get('project.API_AUTH_HEADER');
        $this->endpointStartParse = Config::get('project.API_START_UPDATE_ENDPOINT');
        $this->endpointStopParse = Config::get('project.API_CLEAN_UP_ENDPOINT');
        $this->endpointModel = Config::get('project.API_MODEL_ENDPOINT');
        $this->endpointOffer = Config::get('project.API_OFFER_ENDPOINT');
        $this->endpointConfig = Config::get('project.API_CONFIG_ENDPOINT');
    }


    public function sendStart(string $source): bool
    {
        $response = Http::withHeaders([
                'Authorization'=> $this->auth
            ])
            ->post($this->host . $this->endpointStartParse . '/' . $source);
        if($response->failed())
        {
            return false;
        }
        return true;
    }

    public function sendStop(string $source): bool
    {
        $response = Http::withHeaders([
                'Authorization'=> $this->auth
            ])
            ->post($this->host . $this->endpointStopParse . '/' . $source);
        if($response->failed())
        {
            return false;
        }
        return true;
    }

    public function getModel(string $source_id): bool | \stdClass
    {
        $response = Http::withHeaders([
                'Authorization'=> $this->auth
            ])
            ->get($this->host . $this->endpointModel, [
                'source_id'=>$source_id,
            ]);
        if($response->failed())
        {
            return false;
        }
        $body = $response->body();
        $data = json_decode($body);
        return count($data->items) > 0 ? $data->items[0] : false;
    }

    public function getOffer(SourcesEnum $source, string $sku_id, $show_hidden = true) : bool | \stdClass
    {
        $response = Http::withHeaders([
                'Authorization'=> $this->auth
            ])
            ->get($this->host . $this->endpointOffer, [
                'show_hidden'=>$show_hidden,
                'source'=>$source->value,
                'source_id'=>$sku_id,
            ]);
        if($response->failed())
        {
            return false;
        }
        $body = $response->body();
        $data = json_decode($body);
        return count($data->items) > 0 ? $data->items[0] : false;
    }

    public function updateOffer(string $id, array $data = []) : bool
    {
        $response = Http::withHeaders([
                'Authorization'=> $this->auth
            ])
            ->asMultipart()
            ->patch($this->host . $this->endpointOffer . '/' . $id, $data);
        return !$response->failed();
    }

    public function createOffer(array $data, array $images = []): \stdClass | bool
    {
        $http = Http::withHeaders([
                'Authorization'=> $this->auth
            ]);
        foreach ($images as $index=>$image)
        {
            if(!$image)
            {
                continue;
            }
            $resource = Storage::disk(StorageEnum::PHOTOS->value)->readStream($image);
            if(!is_null($resource))
            {
                $http->attach('photos[]', $resource, 'photo-' . ($index + 1) . '.jpg');
            }
        }
        try {
            $response = $http->post($this->host . $this->endpointOffer, $data);
        }catch (\Exception $exception)
        {
            return false;
        }

        if($response->failed())
        {
            return false;
        }
        $content = $response->body();
        $offer = json_decode($content);
        if(json_last_error() != JSON_ERROR_NONE)
        {
            return false;
        }
        return $offer;
    }

    public function uploadRates(array $rates): bool
    {
        $response = Http::withHeaders([
                'Authorization'=> $this->auth
            ])
            ->asJson()
            ->patch($this->host . $this->endpointConfig . '/rates', [
                'value'=>$rates,
            ]);
        if($response->failed())
        {
            return false;
        }
        return true;
    }

}
