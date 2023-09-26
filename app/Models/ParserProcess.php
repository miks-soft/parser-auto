<?php

namespace App\Models;

use App\Enums\ParserStatusEnum;
use App\Enums\SourcesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ParserProcess extends Model
{
    use AsSource;
    use Filterable;

    protected $fillable = [
        'source', 'status', 'log_id', 'cars_count', 'finished_at', 'pid',
    ];

    protected $dates = [
        'finished_at',
    ];

    protected $casts = [
        'source' => SourcesEnum::class,
        'status'=> ParserStatusEnum::class,
    ];

    protected $allowedFilters = [
        'id',
        'source',
        'status',
        'created_at',
        'finished_at',
    ];

    protected $allowedSorts = [
        'id',
        'source',
        'status',
        'cars_count',
        'created_at',
        'finished_at',
    ];
}
