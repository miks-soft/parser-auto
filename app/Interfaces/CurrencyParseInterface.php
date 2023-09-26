<?php

namespace App\Interfaces;

interface CurrencyParseInterface
{
    public function parse(string $from, string $to = 'CNY'): float;
}
