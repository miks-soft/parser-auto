<?php

namespace App\Facades;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;


/**
 * @package App/Facades
 * @method static UploadedFile | bool download(string $url)
 */
class FileDownload extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'FileDownload';
    }
}
