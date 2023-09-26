<?php

namespace App\Console\Commands;

use App\Models\Proxy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProxyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update proxies from proxies.txt';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $content = file_get_contents(base_path('proxies.txt'));
        $list = explode("\n", $content);
        $list = array_filter($list, function ($item) {
            return strlen(trim($item)) > 0;
        });
        $list = array_map(function ($item) {
            return [
                'address'=> trim($item),
            ];
        }, $list);
        Proxy::with([])
            ->delete();
        DB::table('proxies')
            ->insert($list);
        return Command::SUCCESS;
    }
}
