<?php

namespace App\Console\Commands;

use App\Observers\BlogObserver;
use App\Observers\TestCrawlObserver;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = [RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30];
        $url = 'https://wordpress.test.clarku.edu';

        //# initiate crawler
        Crawler::create($options)
            ->acceptNofollowLinks()
            ->ignoreRobots()
            ->setCrawlObserver(new TestCrawlObserver())
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setDelayBetweenRequests(500)
            ->startCrawling($url);
    }
}
