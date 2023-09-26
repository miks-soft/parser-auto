<?php

namespace App\Services\Parsers;

use App\Classes\ProxyHelper;
use App\Enums\SourcesEnum;
use App\Enums\StorageEnum;
use App\Events\ParserStartUpdate;
use App\Events\ParserStopUpdate;
use App\Facades\APIService;
use App\Facades\FileDownload;
use App\Facades\FileUpload;
use App\Facades\ParserErrorService;
use App\Interfaces\CarParser;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use KubAT\PhpSimple\HtmlDomParser;

class Che168Parser extends MonitorParser implements CarParser
{
    protected string $url = "https://www.che168.com";
    protected int $sleep = 2000000; //микросекунд
    private bool $useProxy;
    private array $specIdMap = [];
    protected SourcesEnum $source = SourcesEnum::CHE168;

    public function __construct()
    {
        $this->useProxy = Config::get('project.PROXY_ENABLED');
        $this->specIdMap = Config::get('che168.spec_map');
    }


    public function search()
    {
        $this->startMonitor();
        ParserStartUpdate::dispatch($this->source);
        $url = $this->url . "/china/a0_0msdgscncgpi1ltocsp1exf4/";
        $isNext = false;
        try {
            do
            {
                $isNext = false;
                $response = $this->getSitePage($url);
                if(is_null($response) || $response->failed())
                {
                    throw new \Exception("Failed che168 list. Url " . $url);
                }
                $content = $response->body();
                $dom = HtmlDomParser::str_get_html($content);
                $err = $dom->find('div.fail-title');
                if($err)
                {
                    sleep(5);
                    $isNext = true;
                    continue;
                }
                $nextButton = $dom->find("a.page-item-next", 0);
                if($nextButton)
                {
                    $url = $this->url . $nextButton->attr['href'];
                    $url_parts = parse_url($url);
                    if(!is_array($url_parts))
                    {
                        break;
                    }
                    $url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
                    if(filter_var($url, FILTER_VALIDATE_URL))
                    {
                        $isNext = true;
                    }
                }
                $carsCards = $dom->find('.content .viewlist_ul .cards-li a.carinfo');
                foreach ($carsCards as $card)
                {
                    $carUrl = $card->attr['href'];
                    $li = $card->parent;
                    if(!isset($li->attr['specid']))
                    {
                        continue;
                    }
                    if(!isset($this->specIdMap[$li->attr['specid']]))
                    {
                        continue;
                    }
                    $car = $this->parseCarPage($carUrl);
                    if($car != null)
                    {
                        $this->publishCar($car);
                    }
                }
                usleep($this->sleep);
            }
            while($isNext);
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

    protected function proxyHttp(): PendingRequest
    {
        $options = [];
        if($this->useProxy)
        {
            $proxy = ProxyHelper::getRandomProxy();
            if($proxy)
            {
                $options['proxy'] = $proxy;
            }
        }
        return Http::withOptions($options);
    }

    private function getSitePage(string $url, $try = 10)
    {
        if($try <= 0)
        {
            return null;
        }
        try {
            $response = $this->proxyHttp()->get($url);
            return $response;
        }catch(\Illuminate\Http\Client\ConnectionException $e)
        {
            sleep(5);
            return $this->getSitePage($url, $try - 1);
        }
    }

    public function parseCarPage(string $realUrl, $try = 10)
    {
        if($try <= 0)
        {
            return null;
        }
        $url = $realUrl;
        if(Str::startsWith($url, '/') && !Str::startsWith($url, '//'))
        {
            $url = $this->url . $url;
        }
        $info = parse_url($url);
        if(!isset($info['host']) || !isset($info['path']))
        {
            return null;
        }
        $url = 'https://' . $info['host'] . $info['path'];
        $path = Str::replace('.html', '', $info['path']);
        $path = trim($path, '/');
        $result = new \stdClass();
        $result->url = $url;
        $result->sourceId = Arr::join(explode('/', $path), '-');
        $result->photos = [];

        usleep($this->sleep);
        try {
            $response = $this->proxyHttp()->get($url);
        }catch(\Illuminate\Http\Client\ConnectionException $e)
        {
            sleep(5);
            return $this->parseCarPage($realUrl, $try - 1);
        }

        if($response->failed())
        {
            return null;
        }
        $content = $response->body();
        $dom = HtmlDomParser::str_get_html($content);
        $err = $dom->find('div.fail-title');
        if($err)
        {
            sleep(5);
            return $this->parseCarPage($realUrl, $try - 1);
        }
        $specInputEl = $dom->find('#car_specid', 0);
        $mileageInputEl = $dom->find('#car_mileage', 0);
        $priceInputEl = $dom->find('#car_price', 0);
        $firstRegistrationEl = $dom->find('#car_firstregtime', 0);
        if(!$specInputEl || !$priceInputEl || !$mileageInputEl)
        {
            return null;
        }
        $result->spec_id = (int)$specInputEl->attr['value'];
        if(!isset($this->specIdMap[$specInputEl->attr['value']]))
        {
            return null;
        }
        $result->model_source_id = $this->specIdMap[$specInputEl->attr['value']];
        $result->mileage = (float)$mileageInputEl->attr['value'];
        $result->price = (float)$priceInputEl->attr['value'];

        if($firstRegistrationEl)
        {
            $result->first_registration = implode("-", explode('/', $firstRegistrationEl->attr['value'] . "/01"));
        }

        $photosCards = $dom->find('.swiper-wrapper .swiper-slide a img');
        foreach ($photosCards as $card)
        {
            if(isset($card->attr['src']))
            {
                $src = $card->attr['src'];
                if(!Str::contains($src, ['default-che168.jpg', 'default-che168.png']))
                {
                    $result->photos []= $src;
                }
            }
        }
        return $result;
    }

    public function publishCar($car)
    {
        $model = APIService::getModel($car->model_source_id);
        if(!$model)
        {
            return;
        }
        $offer = APIService::getOffer($this->source, $car->sourceId);
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
        foreach ($car->photos as $imageUrl)
        {
            $file = FileDownload::download($imageUrl);
            if($file !== false)
            {
                $imagePaths []= FileUpload::upload($file, StorageEnum::PHOTOS->value, $car->sourceId);
            }
        }
        $data = [
            'model_source_id'=>$car->model_source_id,
            'status'=>'active',
            'mileage'=>$car->mileage,
            'price'=>$car->price,
            'source'=>$this->source->value,
            'source_id'=>$car->sourceId,
        ];
        if(isset($car->first_registration))
        {
            $data['first_registration'] = $car->first_registration;
        }
        APIService::createOffer($data, $imagePaths);
        if(Storage::disk(StorageEnum::PHOTOS->value)->exists($car->sourceId))
        {
            Storage::disk(StorageEnum::PHOTOS->value)->deleteDirectory($car->sourceId);
        }
    }

    public function updateOffer($offer, $car)
    {
        $data = [];
        $newPrice = $car->price;
        if($car->model_source_id != $offer->model->source_id)
        {
            $data['model_source_id'] = $car->model_source_id;
        }
        $price_changed = false;
        if(($offer->initial_price * 0.85) <= $newPrice)
        {
            $data['price'] = $newPrice;
            $price_changed = true;
        }
        if($offer->status === 'updating' || $price_changed)
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

    private function normalizeString($str)
    {
        $str = str_replace("&nbsp;", " ", $str);
        $str = preg_replace('/[\s]+/mu', ' ', $str);
        return trim($str);
    }

    /**
     * @return int
     */
    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * @return int
     */
    public function getCars(): int
    {
        return $this->cars;
    }

    /**
     * @return int
     */
    public function getRealCars(): int
    {
        return $this->realCars;
    }



}
