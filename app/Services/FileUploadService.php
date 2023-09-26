<?php

namespace App\Services;

use App\Enums\StorageEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * @param UploadedFile $image
     * @param string $storage
     * @param string $path
     * @return string|null
     */
    public function upload(UploadedFile $image, string $storage, string $path = null)
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        if($path)
        {
            $filename = $path . DIRECTORY_SEPARATOR . $filename;
        }
        try
        {
            $fd = fopen($image->getRealPath(), 'rb');
            Storage::disk($storage)->put($filename, $fd);
        }
        catch (\Exception $exception)
        {
            fclose($fd);
            @unlink($image->getRealPath());
            return null;
        }
        fclose($fd);
        @unlink($image->getRealPath());
        return $filename;
    }

    public function getUrl(string $disk, $value): string | null
    {
        if(is_null($value))
        {
            return null;
        }
        $exists = Storage::disk($disk)->exists($value);
        if(!$exists)
        {
            return null;
        }
        return Storage::disk($disk)->url($value);
    }
}
