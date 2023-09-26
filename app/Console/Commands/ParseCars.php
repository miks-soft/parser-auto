<?php

namespace App\Console\Commands;

use App\Enums\SourcesEnum;
use App\Services\Parsers\ParserFactory;
use Illuminate\Console\Command;

class ParseCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:parse {source}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse cars';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $source = $this->argument('source');
        $num = SourcesEnum::tryFrom($source);
        if(is_null($num))
        {
            $this->error('Invalid source: ' . $source);
            $this->printAcceptSorces();
            return Command::INVALID;
        }
        $parser = ParserFactory::factory($num);
        $this->info('Parse ' . $source . '...');
        $parser->search();
        $this->info('Finish');
        return Command::SUCCESS;
    }

    private function printAcceptSorces()
    {
        $this->info('Accept: ');
        foreach (SourcesEnum::cases() as $case)
        {
            $this->info($case->value);
        }
    }
}
