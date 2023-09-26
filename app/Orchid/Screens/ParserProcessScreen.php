<?php

namespace App\Orchid\Screens;

use App\Enums\ParserStatusEnum;
use App\Facades\ParserProcessService;
use App\Models\ParserProcess;
use App\Orchid\Layouts\ParserProcess\ParserProcessList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ParserProcessScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'processes'=>ParserProcess::query()
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
        return __('messages.parse-process');
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
                ->confirm('Все процессы будут удалены. Продолжить?')
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
            ParserProcessList::class,
        ];
    }

    public function deleteProcess(Request $request, ParserProcess $process)
    {
        if($process->status === ParserStatusEnum::RUNNING)
        {
            Toast::error("Этот процесс ещё запущен. Удаление невозможно");
            return;
        }
        $process->delete();
        Toast::success("Удалено");
    }

    public function deleteAll(Request $request)
    {
        ParserProcess::query()
            ->where('status', '!=', ParserStatusEnum::RUNNING)
            ->delete();
        Toast::success("Удалено");
    }

    public function checkState(Request $request, ParserProcess $process)
    {
        ParserProcessService::checkProcessState($process);
    }

    public function stopProcess(Request $request, ParserProcess $process)
    {
        ParserProcessService::stopProcess($process);
    }
}
