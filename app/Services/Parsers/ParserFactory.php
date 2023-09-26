<?php

namespace App\Services\Parsers;

use App\Enums\SourcesEnum;
use App\Interfaces\CarParser;

class ParserFactory
{
    public static function factory(SourcesEnum $source): CarParser
    {
        return match ($source) {
            SourcesEnum::DONGCHEDI => new DongchediParser(),
            SourcesEnum::CHE168 => new Che168Parser(),
            default => throw new \Exception('Unknown source ' . $source->value)
        };
    }
}
