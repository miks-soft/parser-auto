<?php

namespace App\Console\Commands;

use App\Facades\APIService;
use App\Facades\CurrencyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CurrencyParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse currencies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $eur = CurrencyService::parse('EUR');
        $usd = CurrencyService::parse('USD');
        $usd_rub = CurrencyService::parse('USD', 'RUB');
        $scale = Config::get('project.RATES_SCALE');
        $digits = 4;
        $rates = [
            'EUR'=>round($eur * $scale, $digits),
            'USD'=>round($usd * $scale, $digits),
            'USD_RUB'=>round($usd_rub * $scale, $digits),
        ];
        $res = APIService::uploadRates($rates);
        if($res)
        {
            $this->info('Success');
        }
        else
        {
            $this->error('Error');
        }
        return Command::SUCCESS;
    }
}
