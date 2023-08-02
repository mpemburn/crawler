<?php

namespace App\Services;
use App\Models\BlogList;
use App\Observers\BasicCrawlObserver;
use App\Observers\BlogObserver;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Spatie\Async\Pool;
use Spatie\Crawler\Crawler;
use Throwable;

class CrawlerService
{
    protected Collection $processes;

    public function __construct()
    {
        $this->processes = collect();
    }

    public function loadCrawlProcesses(bool $echo = false): self
    {
        $url = 'https://wordpress.test.clarku.edu';
        $this->processes->push($this->fetchContent($url));

        return $this;
    }

    public function fetchContent($url) {
        $options = [RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30];

        //# initiate crawler
        Crawler::create($options)
            ->acceptNofollowLinks()
            ->ignoreRobots()
            ->setCrawlObserver(new BasicCrawlObserver())
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setDelayBetweenRequests(500)
            ->startCrawling($url);
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

}
