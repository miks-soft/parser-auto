<?php

namespace App\Orchid\Screens\Logs;

use App\Models\ParserLog;
use App\Orchid\Layouts\Logs\LogsList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class LogsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'logs'=>ParserLog::query()
                ->filters()
                ->defaultSort('id', 'desc')
                ->paginate(Config::get('project.TABLE_ROW_LIMIT_DEFAULT')),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('messages.logs');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.site.logs',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Очистить')
                ->icon('trash')
                ->confirm('Все логи парсеров будут удалены. Продолжить?')
                ->method('deleteAll'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            LogsList::class,
        ];
    }

    public function deleteLog(Request $request, ParserLog $log)
    {
        $log->delete();
        Toast::success("Удалено");
    }

    public function deleteAll(Request $request)
    {
        ParserLog::query()->delete();
        Toast::success("Удалено");
    }
}
