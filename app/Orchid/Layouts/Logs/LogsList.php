<?php

namespace App\Orchid\Layouts\Logs;

use App\Enums\SourcesEnum;
use App\Models\ParserLog;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class LogsList extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'logs';

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
        return [
            TD::make('id', 'ID')
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (ParserLog $log) => $log->id),

            TD::make('source', "Ресурс")
                ->sort()
                ->cantHide()
                ->filter(TD::FILTER_SELECT, $sources)
                ->render(fn (ParserLog $log) => $log->source->value),

            TD::make('error', "Ошибка")
                ->sort()
                ->render(fn (ParserLog $log) => $this->prepareText($log->error)),



            TD::make('created_at', "Создан")
                ->sort()
                ->filter(DateRange::make())
                ->render(fn (ParserLog $log) => $log->created_at->toDateTimeString()),

            TD::make('view', "Показать")
                ->cantHide()
                ->render(
                    fn (ParserLog $log) => Link::make('View')
                        ->route('platform.logs.view', $log->id)
                        ->icon('eye'),
                ),


            TD::make('delete', "Удалить")
                ->cantHide()
                ->render(
                    fn (ParserLog $log) => Button::make('Удалить')
                        ->icon('trash')
                        ->class('btn btn-link text-danger')
                        ->confirm('Запись будет удалена безвозвратно. Вы действительно хотите удалить?')
                        ->method('deleteLog', ['log' => $log->id]),
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
