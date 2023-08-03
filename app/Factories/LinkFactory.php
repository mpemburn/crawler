<?php

namespace App\Factories;

use App\Interfaces\FindableLink;
use App\Models\WwwDevBrokenPage;
use App\Models\WwwProductionBrokenPage;
use App\Models\WwwTestingBrokenPage;
use App\Models\WordPressProductionBrokenPage;
use App\Models\WordPressTestBrokenPage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LinkFactory
{
    public static function build(string $env): FindableLink
    {
        return match ($env) {
            'wwwdev' => new WwwDevBrokenPage(),
            'wwwprod' => new WwwProductionBrokenPage(),
            'wwwtest' => new WwwTestingBrokenPage(),
            'wordpressprod' => new WordPressProductionBrokenPage(),
            'wordpresstest' => new WordPressTestBrokenPage(),
            default => throw new ModelNotFoundException('No valid Model specified by "' . $env . '"'),
        };
    }
}
