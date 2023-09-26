<?php

namespace App\Services\Parsers;

use App\Enums\SourcesEnum;
use App\Enums\StorageEnum;
use App\Events\ParserStartUpdate;
use App\Events\ParserStopUpdate;
use App\Facades\APIService;
use App\Facades\FileDownload;
use App\Facades\FileUpload;
use App\Facades\ParserErrorService;
use App\Interfaces\CarParser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DongchediParser extends MonitorParser implements CarParser
{
    protected string $url = "https://www.dongchedi.com";
    protected int $sleep = 1500000; //микросекунд
    protected SourcesEnum $source = SourcesEnum::DONGCHEDI;

    public function search()
    {
        $this->startMonitor();
        ParserStartUpdate::dispatch($this->source);
        $page = 1;
        $cars = [];
        try {
            do
            {
                $cars = [];
                $response = Http::asForm()
                    ->post($this->url . '/motor/pc/sh/sh_sku_list', [
                        'fuel_form'=>4,
                        'page'=>$page,
                        'limit'=>80,
                    ]);
                if($response->failed())
                {
                    throw new \Exception("Failed dongchedi list. Page " . $page);
                }
                $body = $response->body();
                $data = json_decode($body);
                $cars = $data->data->search_sh_sku_info_list;
                foreach ($cars as $car)
                {
                    $carData = $this->parseCar($car->sku_id);
                    if($carData !== false)
                    {
                        $this->publishCar($carData);
                    }
                }

                $page++;
                usleep($this->sleep);
            }
            while(count($cars) > 0);
            $this->stopMonitor();
        }catch (\Exception $exception)
        {
            $err = ParserErrorService::store($this->source, $exception);
            $this->stopMonitor($err->id);
        }
        if(Config::get('project.IS_DELETE_AFTER_PARSE'))
        {
            ParserStopUpdate::dispatch($this->source);
        }

    }

    public function parseCar(int $sku_id)
    {
        $symbols = [
            ''=> 0,
            ''=> 1,
            ''=> 2,
            ''=> 3,
            ''=> 4,
            ''=> 5,
            ''=> 6,
            ''=> 8,
            ''=> 7,
            ''=> 9,
            ''=> '',
            ''=> '',
            ''=> '',
            '.'=> '.',
        ];
        $response = Http::get("https://www.dongchedi.com/motor/pc/sh/detail/major", [
            'sku_id'=>$sku_id,
        ]);
        if($response->failed())
        {
            return false;
        }
        $body = $response->body();
        $data = json_decode($body);
        if(json_last_error() != JSON_ERROR_NONE || is_null($data))
        {
            return false;
        }
        $data = $data->data;
        $mileageParts = collect(mb_str_split($data->car_info->mileage))
            ->map(static fn($item) => $symbols[$item] ?? $item);
        $priceParts = collect(mb_str_split($data->sh_price))
            ->map(static fn($item) => $symbols[$item] ?? $item);

        $data->car_info->mileage = $mileageParts->join('');
        $data->sh_price = $priceParts->join('');
        if(isset($data->other_params))
        {
            $first_registration_param = collect($data->other_params)
                ->filter(function ($value) {
                    return $value->name === '上牌时间';
                })
                ->first();
            if(!is_null($first_registration_param))
            {
                $value = Str::replace('月', '', $first_registration_param->value);
                $valueParts = explode('年', $value);
                if($first_registration_param->value !== '未上牌' && count($valueParts) == 2)
                {
                    $data->first_registration = "{$valueParts[0]}-{$valueParts[1]}-01";
                }
            }
        }
        return $data;
    }

    public function publishCar($car)
    {
        if(!isset($car->car_info) || !isset($car->car_info->car_id))
        {
            return;
        }
        $model = APIService::getModel($car->car_info->car_id);
        if(!$model)
        {
            return;
        }
        $offer = APIService::getOffer($this->source, $car->sku_id);
        if($offer)
        {
            $this->updateOffer($offer, $car);
        }
        else
        {
            $this->createOffer($car);
        }
        $this->addCount();
    }

    public function createOffer($car)
    {
        $imagePaths = [];
        foreach ($car->head_images as $imageUrl)
        {
            $file = FileDownload::download($imageUrl);
            if($file !== false)
            {
                $imagePaths []= FileUpload::upload($file, StorageEnum::PHOTOS->value, $car->sku_id);
            }
        }
        $data = [
            'model_source_id'=>$car->car_info->car_id,
            'status'=>'active',
            'mileage'=>$car->car_info->mileage,
            'price'=>$car->sh_price,
            'source'=>$this->source->value,
            'source_id'=>$car->sku_id
        ];
        if(isset($car->first_registration))
        {
            $data['first_registration'] = $car->first_registration;
        }
        APIService::createOffer($data, $imagePaths);
        if(Storage::disk(StorageEnum::PHOTOS->value)->exists($car->sku_id))
        {
            Storage::disk(StorageEnum::PHOTOS->value)->deleteDirectory($car->sku_id);
        }
    }

    public function updateOffer($offer, $car)
    {
        $data = [];
        $newPrice = $car->sh_price;
        if($car->car_info->car_id != $offer->model->source_id)
        {
            $data['model_source_id'] = $car->car_info->car_id;
        }
        if(($offer->initial_price * 0.85) <= $newPrice)
        {
            $data['price'] = $newPrice;
        }
        if($offer->status === 'updating')
        {
            $data['status'] = 'active';
        }
        if(isset($car->first_registration))
        {
            $data['first_registration'] = $car->first_registration;
        }
        if(count($data) > 0)
        {
            APIService::updateOffer($offer->id, $data);
        }
    }

}
