<?php

namespace App\Facades;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;


/**
 * @package App/Facades
 * @method static string upload(UploadedFile $image, string $storage, string $path = null)
 * @method static string | null getUrl(string $disk, $value)
 */
class FileUpload extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'FileUpload';
    }
}
