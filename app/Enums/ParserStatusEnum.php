<?php

namespace App\Enums;

enum ParserStatusEnum: string
{
    case RUNNING = "running";
    case FINISHED = "success";
    case ERROR = "error";
    case EXIT = "exit";
}
