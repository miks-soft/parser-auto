<?php
namespace App\Classes;

class Process
{
    private $pid;
    private $command;

    public function __construct($cl=false)
    {
        if ($cl != false){
            $this->command = $cl;
            $this->runCom();
        }
    }
    private function runCom()
    {
        if(self::isWindows())
        {
            $items = explode(" ", $this->command);
            $command = $items[0];
            array_shift($items);
            $arguments = "";
            if(count($items) > 0)
            {
                $argumentsList = implode(" ", $items);
                $argumentsList = escapeshellarg($argumentsList);
                $arguments = ' -ArgumentList \" ' . $argumentsList . '\"';
            }
            $exec = 'powershell.exe "(Start-Process ' . $command . $arguments . ' -passthru -WindowStyle Hidden).ID"';
            $this->pid = exec($exec);
        }
        else
        {
            $command = 'nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
            exec($command ,$op);
            $this->pid = (int)$op[0];
        }

    }

    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function status()
    {
        if(self::isWindows())
        {
            $output = array_filter(explode(" ", shell_exec("wmic process get processid | find \"{$this->pid}\"")));
            if(count($output) > 0)
            {
                return true;
            }
            return false;
        }
        else
        {
            $command = 'ps -p '.$this->pid;
            exec($command,$op);
            if (!isset($op[1]))return false;
            else return true;
        }

    }

    public function start()
    {
        if ($this->command != '')$this->runCom();
        else return true;
    }

    public function stop()
    {
        if(self::isWindows())
        {
            $command = 'taskkill /pid '. $this->pid . ' /F';
            exec($command);
            if ($this->status() == false)return true;
            else return false;
        }
        else
        {
            $command = 'kill '.$this->pid;
            exec($command);
            if ($this->status() == false)return true;
            else return false;
        }

    }

    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
