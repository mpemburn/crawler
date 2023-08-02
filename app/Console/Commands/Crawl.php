<?php

namespace App\Console\Commands;

use App\Observers\BlogObserver;
use App\Observers\BasicCrawlObserver;
use App\Services\CrawlerService;
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
        (new CrawlerService())->loadCrawlProcesses()->run();
    }
}
