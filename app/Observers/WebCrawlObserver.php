<?php

namespace App\Observers;

use App\Interfaces\FindableLink;
use App\Services\CrawlerService;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;

class WebCrawlObserver extends CrawlObserver
{
    public const ERROR_CODES = [
        0 => 'n/a',
        301 => '301 Moved',
        302 => '302 Gone ',
        401 => '401 Unauthorized',
        404 => '404 Not Found',
        410 => '410 Gone',
        500 => '500 Fatal Error',
    ];

    protected FindableLink $finder;
    protected array $row;
    protected bool $echo;

    public function setFinder(FindableLink $finder): self
    {
        $this->finder = $finder;

        return $this;
    }

    public function setRow(array $row): self
    {
        $this->row = $row;

        return $this;
    }

    public function verbose(bool $echo): self
    {
        $this->echo = $echo;

        return $this;
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

        if ($this->echo) {
            echo 'Testing...' . $url;
        }

        $result = (new CrawlerService($this))->testUrl($url);

        $linkFinder = new $this->finder();

        $linkFinder->create([
            'blog_id' => $this->row['blog_id'],
            'page_url' => $url,
            'error' => $result === 200 ? 'success' : $result . ' Error',
        ]);

        if ($this->echo) {
            $emoji = $result === 200 ? ' 👍' : ' 👎';
            echo $emoji . PHP_EOL;
        }
    }

    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null): void
    {
        if (! $this->finder->matchesBasePath($url)) {
            return;
        }

        echo 'Failed crawl: ' . $url . PHP_EOL;
        $code = $requestException->getCode();

        $linkFinder = new $this->finder();

        $linkFinder->create([
            'blog_id' => $this->row['blog_id'],
            'page_url' => $url,
            'error' => self::ERROR_CODES[$code] ?? $code,
        ]);
    }
}
