<?php

namespace Database\Seeders;

use App\Models\SiteSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'slug'=>'API_HOST',
                'value'=>"https://api.ev.wiki",
                'title'=>'API хост'
            ],
            [
                'slug'=>'API_AUTH_HEADER',
                'value'=>"Basic cHJvamVjdElzTm90OnBhaWRJbkZ1bGw=",
                'title'=>'API ключ'
            ],
            [
                'slug'=>'API_START_UPDATE_ENDPOINT',
                'value'=>"/api/offer/start-update",
                'title'=>'Эндпоинт уведомления о старте'
            ],
            [
                'slug'=>'API_CLEAN_UP_ENDPOINT',
                'value'=>"/api/offer/clean-up",
                'title'=>'Эндпоинт уведомления о завершении'
            ],
            [
                'slug'=>'API_MODEL_ENDPOINT',
                'value'=>"/api/model",
                'title'=>'Эндпоинт получения модели авто'
            ],
            [
                'slug'=>'API_OFFER_ENDPOINT',
                'value'=>"/api/offer",
                'title'=>'Эндпоинт предложений'
            ],
            [
                'slug'=>'API_CONFIG_ENDPOINT',
                'value'=>"/api/config",
                'title'=>'Эндпоинт конфигов'
            ],
            [
                'slug'=>'PROXY_ENABLED',
                'value'=>1,
                'value_type'=>'boolean',
                'title'=>'Использовать прокси'
            ],
            [
                'slug'=>'HTTP_RETRY',
                'value'=>3,
                'value_type'=>'integer',
                'title'=>'Сколько раз пытаться выполнить запрос'
            ],
            [
                'slug'=>'HTTP_RETRY_TIMEOUT',
                'value'=>5,
                'value_type'=>'integer',
                'title'=>'Интервал между попытками сделать запрос (сек)'
            ],
            [
                'slug'=>'RAPID_API_KEY',
                'value'=>'baf73dddbemsh3eb7fd643553521p1f5225jsn1db6ec604471',
                'value_type'=>'string',
                'title'=>'Ключ доступа для rapidapi.com'
            ],
            [
                'slug'=>'RATES_SCALE',
                'value'=> 0.9925,
                'value_type'=>'float',
                'title'=>'Коэффициент курсов валют'
            ],
            [
                'slug'=>'IS_DELETE_AFTER_PARSE',
                'value'=> 0,
                'value_type'=>'boolean',
                'title'=>'Удалять старые объявления после прохода парсера'
            ],
        ];
        foreach ($settings as $setting)
        {
            SiteSettings::query()
                ->firstOrCreate([
                    'slug'=>$setting['slug'],
                ], $setting);
        }
    }
}
