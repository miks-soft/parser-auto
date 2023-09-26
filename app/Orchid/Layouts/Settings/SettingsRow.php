<?php

namespace App\Orchid\Layouts\Settings;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class SettingsRow extends Rows
{

    private $settings;
    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        $this->settings = $this->query->get('settings');
        $fields = [];
        foreach ($this->settings as $setting)
        {
            switch ($setting->value_type)
            {
                case 'integer':
                {
                    $fields []= Input::make('settings.'.$setting->slug)
                        ->title($setting->title)
                        ->type('number')
                        ->value($setting->value);
                    break;
                }
                case 'float':
                {
                    $fields []= Input::make('settings.'.$setting->slug)
                        ->title($setting->title)
                        ->type('number')
                        ->value($setting->value)
                        ->step(0.0001);
                    break;
                }
                case 'string':
                {
                    $fields []= TextArea::make('settings.'.$setting->slug)
                        ->title($setting->title)
                        ->rows(5)
                        ->value($setting->value);
                    break;
                }
                case 'boolean':
                {
                    $fields []= CheckBox::make('settings.'.$setting->slug)
                        ->sendTrueOrFalse()
                        ->title($setting->title)
                        ->value($setting->value);
                    break;
                }
                default:break;
            }
        }
        return $fields;
    }
}
