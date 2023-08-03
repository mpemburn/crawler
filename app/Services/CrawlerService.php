<?php

namespace App\Services;
use App\Interfaces\FindableLink;
use App\Models\BlogList;
use App\Observers\BasicCrawlObserver;
use App\Observers\BlogObserver;
use App\Observers\WebCrawlObserver;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Spatie\Async\Pool;
use Spatie\Crawler\Crawler;
use Throwable;

class CrawlerService
{
    protected Collection $processes;
    protected FindableLink $finder;
    protected bool $echo;

    public function __construct()
    {
        $this->processes = collect();
    }

    public function setEnvironment(FindableLink $finder): self
    {
        $this->finder = $finder;

        return $this;
    }

    public function verbose(bool $echo)
    {
        $this->echo = $echo;

        return $this;
    }

    public function loadCrawlProcesses(Collection $rows, bool $echo = false): self
    {
        $rows->each(function ($row) use ($echo) {
            $this->processes->push($this->fetchContent($row));
        });

        return $this;
    }

    public function fetchContent($row) {
        $options = [RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30];

        $observer = (new WebCrawlObserver())
            ->setRow($row)
            ->setFinder($this->finder)
            ->verbose($this->echo);

        //# initiate crawler
        Crawler::create($options)
            ->acceptNofollowLinks()
            ->ignoreRobots()
            ->setCrawlObserver($observer)
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setDelayBetweenRequests(500)
            ->startCrawling($row['url']);
        return true;
    }

    public function run()
    {
        $pool = Pool::create();

        $this->processes->each(function ($process) use (&$pool) {
            $pool->add(function () use ($process) {
                // Do a thing
            })->then(function ($output) {
                // Handle success
            })->catch(function (Throwable $exception) {
                // Handle exception
            });
        });

        $pool->wait();
    }

    public function testUrl(string $url, ?string $username = null, ?string $password = null): int
    {
        return (new UrlService())->testUrl($url, $username, $password);
    }


}
