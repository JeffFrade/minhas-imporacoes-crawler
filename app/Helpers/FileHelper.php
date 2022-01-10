<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * @param string $content
     * @param string $filename
     * @param string $disk
     * @return void
     */
    public static function writeArchive(string $content, string $filename, string $disk = 'public')
    {
        Storage::disk($disk)->put($filename, $content, 'public');
    }

    /**
     * @return void
     */
    public static function clearCookiesFolder()
    {
        shell_exec(sprintf('rm -rf %s/*', storage_path('app/public/cookies')));
    }
}
