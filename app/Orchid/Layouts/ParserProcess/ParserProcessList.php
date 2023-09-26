<?php

namespace App\Orchid\Layouts\ParserProcess;

use App\Enums\ParserStatusEnum;
use App\Enums\SourcesEnum;
use App\Models\ParserProcess;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ParserProcessList extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'processes';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $sources = [];
        foreach (SourcesEnum::cases() as $enum)
        {
            $sources[$enum->value] = $enum->value;
        }
        $statuses = [];
        foreach (ParserStatusEnum::cases() as $enum)
        {
            $statuses[$enum->value] = $enum->value;
        }
        return [
            TD::make('id', 'ID')
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (ParserProcess $process) => $process->id),

            TD::make('source', "Ресурс")
                ->sort()
                ->cantHide()
                ->filter(TD::FILTER_SELECT, $sources)
                ->render(fn (ParserProcess $process) => $process->source->value),

            TD::make('status', "Статус")
                ->sort()
                ->filter(TD::FILTER_SELECT, $statuses)
                ->render(fn (ParserProcess $process) => $process->status->value),

            TD::make('cars_count', "Объявлений найдено")
                ->sort()
                ->render(fn (ParserProcess $process) => $process->cars_count),

            TD::make('error', "Ошибка")
                ->render(function (ParserProcess $process) {
                    if(is_null($process->log_id))
                    {
                        return '';
                    }
                    return Link::make('Error')
                        ->route('platform.logs.view', $process->log_id)
                        ->icon('eye');
                }),

            TD::make('pid', "PID")
                ->render(function (ParserProcess $process) {
                    return $process->pid;
                }),

            TD::make('check', "Состояние")
                ->cantHide()
                ->render(
                    function (ParserProcess $process) {
                        if($process->status != ParserStatusEnum::RUNNING)
                        {
                            return '';
                        }
                        return Button::make('проверить')
                            ->class('btn btn-link text-info')
                            ->method('checkState', ['process' => $process->id]);
                    }),

            TD::make('stop', "Завершить")
                ->cantHide()
                ->render(
                    function (ParserProcess $process) {
                        if($process->status != ParserStatusEnum::RUNNING)
                        {
                            return '';
                        }
                        return Button::make('остановить')
                            ->class('btn btn-link text-danger')
                            ->method('stopProcess', ['process' => $process->id]);
                    }),

            TD::make('created_at', "Создан")
                ->sort()
                ->filter(DateRange::make())
                ->render(fn (ParserProcess $process) => $process->created_at->toDateTimeString()),

            TD::make('finished_at', "Завершён")
                ->sort()
                ->filter(DateRange::make())
                ->render(fn (ParserProcess $process) => $process->finished_at?->toDateTimeString()),

            TD::make('delete', "Удалить")
                ->cantHide()
                ->render(
                    fn (ParserProcess $process) => Button::make('Удалить')
                        ->icon('trash')
                        ->class('btn btn-link text-danger')
                        ->confirm('Запись будет удалена безвозвратно. Вы действительно хотите удалить?')
                        ->method('deleteProcess', ['process' => $process->id]),
                ),
        ];
    }
    private function prepareText($text)
    {
        if(mb_strlen($text) > 20)
        {
            $text = mb_strcut($text, 0, 17) . '...';
        }
        return $text;
    }
}
