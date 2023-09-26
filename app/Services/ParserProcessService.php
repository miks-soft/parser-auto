<?php

namespace App\Services;

use App\Classes\Process;
use App\Enums\ParserStatusEnum;
use App\Enums\SourcesEnum;
use App\Models\ParserProcess;
use Illuminate\Support\Carbon;

class ParserProcessService
{
    public function start(SourcesEnum $source): ParserProcess
    {
        $pid = getmypid();
        return ParserProcess::query()
            ->create([
                'source'=>$source,
                'status'=>ParserStatusEnum::RUNNING,
                'pid'=> $pid !== false ? $pid : null,
            ])->refresh();
    }

    public function finish(ParserProcess $process, int $cars, int $log_id = null): ParserProcess
    {
        $status = is_null($log_id) ? ParserStatusEnum::FINISHED : ParserStatusEnum::ERROR;
        $process->update([
            'status'=>$status,
            'cars_count'=>$cars,
            'log_id'=>$log_id,
            'finished_at'=>Carbon::now(),
        ]);
        return $process;
    }

    public function checkProcessState(ParserProcess $process)
    {
        if (is_null($process->pid) || $process->status != ParserStatusEnum::RUNNING)
        {
            return;
        }
        $systemProccess = new Process();
        $systemProccess->setPid($process->pid);
        if(!$systemProccess->status())
        {
            $process->update([
                'status'=>ParserStatusEnum::EXIT,
            ]);
        }
    }
    public function stopProcess(ParserProcess $process)
    {
        if (is_null($process->pid) || $process->status != ParserStatusEnum::RUNNING)
        {
            return;
        }
        $systemProccess = new Process();
        $systemProccess->setPid($process->pid);
        $systemProccess->stop();
        $this->checkProcessState($process);
    }
}
