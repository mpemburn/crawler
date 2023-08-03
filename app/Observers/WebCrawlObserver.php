<?php

namespace App\Observers;

use App\Interfaces\FindableLink;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;

class WebCrawlObserver extends CrawlObserver
{
    protected FindableLink $finder;

    public function setFinder(FindableLink $finder)
    {
        $this->finder = $finder;
    }

    public function crawled(
        UriInterface      $url,
        ResponseInterface $response,
        ?UriInterface     $foundOnUrl = null
    ): void
    {
        if (! $this->finder->matchesBasePath($url)) {
            return;
        }

        echo 'Checking: ' . $url . PHP_EOL;
    }

    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null): void
    {
        echo 'Failed crawl: ' . $url . PHP_EOL;
    }
}
