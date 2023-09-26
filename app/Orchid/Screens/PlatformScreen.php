<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Enums\SourcesEnum;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $sources = [];
        foreach (SourcesEnum::cases() as $enum)
        {
            $sources[$enum->value] = [
                'value' => '',
            ];
        }
        return [
            'metrics'=>[
                ...$sources,
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'EV-WIKI';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Парсер объявлений';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        $metrics = [];
        foreach (SourcesEnum::cases() as $enum)
        {
            $metrics[Str::upper($enum->value)] = 'metrics.' . $enum->value;
        }
        return [
            Layout::metrics($metrics),
        ];
    }
}
