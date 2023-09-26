<?php

namespace App\Models;

use App\Enums\SourcesEnum;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ParserLog extends Model
{
    use AsSource;
    use Filterable;
    protected $fillable = [
        'source', 'error', 'full',
    ];

    protected $casts = [
        'source' => SourcesEnum::class
    ];

    protected $allowedFilters = [
        'id',
        'source',
        'created_at',
    ];

    protected $allowedSorts = [
        'id',
        'source',
        'created_at',
    ];
}
