<?php

namespace App\Services;
use Illuminate\Support\Collection;

class FileService
{
    static function toArray(string $filename): ?array
    {
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);

            return explode("\n", $contents);
        }

        return null;
    }

    static function toCollection(string $filename): ?Collection
    {
        $contents = self::toArray($filename);
        if ($contents) {
            return collect($contents);
        }

        return null;
    }
}
