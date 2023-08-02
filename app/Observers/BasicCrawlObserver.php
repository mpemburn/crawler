<?php

namespace App\Observers;

use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;

class BasicCrawlObserver extends CrawlObserver
{

    public function crawled(
        UriInterface      $url,
        ResponseInterface $response,
        ?UriInterface     $foundOnUrl = null
    ): void
    {
        echo 'Checking: ' . $url . PHP_EOL;
    }

    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null): void
    {
        echo 'Failed crawl: ' . $url . PHP_EOL;
    }
}
