<?php

namespace App\Console\Commands;

use App\Models\UserAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserAgentUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-agent:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user-angets from user-agents.txt';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $content = file_get_contents(base_path('user-agents.txt'));
        $list = explode("\n", $content);
        $list = array_filter($list, function ($item) {
            return strlen(trim($item)) > 0;
        });
        $list = array_map(function ($item) {
            return [
                'agent'=> trim($item),
            ];
        }, $list);
        UserAgent::with([])
            ->delete();
        DB::table('user_agents')
            ->insert($list);
        return Command::SUCCESS;
    }
}
