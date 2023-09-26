<?php

namespace App\Orchid\Screens\Logs;

use App\Models\ParserLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class LogViewScreen extends Screen
{
    /**
     * @var ParserLog
     */
    public $log;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param ParserLog $log
     * @return array
     */
    public function query(ParserLog $log): iterable
    {
        $this->log = $log;
        return [
            'log' => $log,
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.site.logs',
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

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm('Запись будет удалена безвозвратно. Вы действительно хотите удалить?')
                ->method('remove'),
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
            Layout::legend('log', [
                Sight::make('id', 'ID'),
                Sight::make('created_at', 'Дата')->render(fn(ParserLog $log) => $log->created_at->toDateTimeString()),
            ]),

            Layout::view('text-block', [
                'title' => 'Ошибка',
                'message' => $this->log->error,
            ]),

            Layout::rows([
                Code::make('log.full')
                    ->title('Полная ошибка')
                    ->readonly(true)
                    ->height('500px')
            ]),
        ];
    }

    public function remove(ParserLog $log): RedirectResponse
    {
        $log->delete();

        Toast::info(__('messages.deleted'));

        return redirect()->route('platform.logs');
    }
}
