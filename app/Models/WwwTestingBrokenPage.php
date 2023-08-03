<?php

namespace App\Models;

/**
 * @method  where(string $string, $blog_id)
 */
class WwwTestingBrokenPage extends Link
{
    public const AUTH_USERNAME = 'WWWDEV_USERNAME';
    public const AUTH_PASSWORD = 'WWWDEV_PASSWORD';

    protected $fillable = [
        'blog_id',
        'page_url',
        'error'
    ];

    public $table = 'www_testing_broken_pages';
    protected string $site = 'www';
    protected string $blogBasePath = 'www.testing.clarku.edu';
}
