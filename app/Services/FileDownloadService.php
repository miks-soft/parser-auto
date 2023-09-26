<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class FileDownloadService
{
    public static function download(string $url): UploadedFile | bool
    {
        try{
            $response = Http::retry(1, 100, null, false)->get($url);
            if($response->failed())
            {
                return false;
            }
            $content = $response->body();
            $tmpfile = tempnam(sys_get_temp_dir(), "file_");
            file_put_contents($tmpfile, $content);
            $info = pathinfo($url);
            return new UploadedFile($tmpfile, $info['basename']);
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}
