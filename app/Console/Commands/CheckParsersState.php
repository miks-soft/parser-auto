<?php

namespace App\Console\Commands;

use App\Classes\Process;
use App\Enums\ParserStatusEnum;
use App\Enums\SourcesEnum;
use App\Facades\ParserProcessService;
use App\Models\ParserProcess;
use Illuminate\Console\Command;

class CheckParsersState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check parser state';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ParserProcess::query()
            ->where('status', ParserStatusEnum::RUNNING)
            ->whereNotNull('pid')
            ->eachById(function (ParserProcess $process) {
                ParserProcessService::checkProcessState($process);
            });
        foreach (SourcesEnum::cases() as $enum)
        {
            $exists = ParserProcess::query()
                ->where('status', ParserStatusEnum::RUNNING)
                ->where('source', $enum)
                ->exists();
            if(!$exists)
            {
                $process = new Process('php artisan cars:parse ' . $enum->value);
            }
        }
        return Command::SUCCESS;
    }
}
