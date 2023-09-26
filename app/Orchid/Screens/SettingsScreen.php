<?php

namespace App\Orchid\Screens;

use App\Http\Requests\SettingsUpdateRequest;
use App\Models\SiteSettings;
use App\Orchid\Layouts\Settings\SettingsRow;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class SettingsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'settings'=>SiteSettings::with([])->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Settings';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('check')
                ->method('save'),
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
            SettingsRow::class,
        ];
    }

    public function save(SettingsUpdateRequest $request)
    {
        $settings = $request->input('settings', []);
        foreach ($settings as $key=>$value)
        {
            $setting = SiteSettings::query()
                ->where('slug', $key)->first();
            if(is_null($setting))
            {
                continue;
            }
            $setting->update([
                'value'=>$value
            ]);
        }
        Toast::success(__('Настройки успешно сохранены'));
    }
}
