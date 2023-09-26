<?php

namespace App\Providers;

use App\Models\SiteSettings;
use App\Services\APIService;
use App\Services\CurrencyParseService;
use App\Services\FileDownloadService;
use App\Services\FileUploadService;
use App\Services\ErrorLogService;
use App\Services\ParserProcessService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Client\Factory as BaseFactory;
use App\Classes\CustomHttp;
use Illuminate\Events\Dispatcher;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            BaseFactory::class,
            function ($app) {
                return new CustomHttp($app->make(Dispatcher::class));
            }
        );

        $this->app->singleton('CurrencyService', CurrencyParseService::class);
        $this->app->singleton('ParserErrorService', ErrorLogService::class);
        $this->app->singleton('ParserProcessService', ParserProcessService::class);
        $this->app->singleton('APIService', APIService::class);
        $this->app->singleton('FileDownload', FileDownloadService::class);
        $this->app->singleton('FileUpload', FileUploadService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try
        {
            $settings = SiteSettings::all();
            foreach ($settings as $setting)
            {
                if(is_null($setting->config))
                {
                    Config::set('project.'.$setting->slug, $setting->value);
                }
                else
                {
                    Config::set($setting->config, $setting->value);
                }

            }
        }
        catch (\Exception $exception)
        {}
    }
}
