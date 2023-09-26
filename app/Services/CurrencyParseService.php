<?php

namespace App\Services;

use App\Interfaces\CurrencyParseInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CurrencyParseService implements CurrencyParseInterface
{
    protected $url = 'https://currency-converter5.p.rapidapi.com/currency/convert';

    public function parse(string $from, string $to = 'CNY'): float
    {
        $response = Http::withHeaders([
                'x-rapidapi-host'=>'currency-converter5.p.rapidapi.com',
                'x-rapidapi-key'=>Config::get('project.RAPID_API_KEY')
            ])
            ->get($this->url, [
                'from'=>Str::upper($from),
                'to'=>Str::upper($to),
            ]);
        if($response->failed())
        {
            throw new \Exception("Currency parser error: " . $response->body());
        }
        $data = json_decode($response->body(), true);
        $amount = (float)$data['amount'];
        if(!isset($data['rates'][$to]))
        {
            throw new \Exception("Currency parser error: unknown currency " . $to);
        }
        $rate = (float)$data['rates'][$to]['rate'];
        $rate = $rate * $amount;
        return $rate;
    }

}
