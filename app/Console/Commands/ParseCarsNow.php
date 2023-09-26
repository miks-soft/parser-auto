<?php

namespace App\Console\Commands;

use App\Classes\Process;
use App\Enums\SourcesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ParseCarsNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:parse-now';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start all parsers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (SourcesEnum::cases() as $enum)
        {
            $process = new Process('php artisan cars:parse ' . $enum->value);
        }
        return Command::SUCCESS;
    }
}
