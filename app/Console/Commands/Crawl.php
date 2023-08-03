<?php

namespace App\Console\Commands;

use App\Factories\LinkFactory;
use App\Interfaces\FindableLink;
use App\Observers\BlogObserver;
use App\Observers\BasicCrawlObserver;
use App\Services\CrawlerService;
use App\Services\FileService;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use PDOException;
use Spatie\Crawler\Crawler;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl {--env=}';

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
        $env = $this->option('env');
        $finder = $this->getLinkFinder($env);

        $file = Storage::path('blogs.txt');
        $contents = FileService::toCollection($file);

        (new CrawlerService())->setEnvironment($finder)
            ->loadCrawlProcesses($contents)
            ->run();
    }

    protected function getLinkFinder($env): ?FindableLink
    {
        try {
            return LinkFactory::build($env);
        } catch (PDOException $pdoex) {
            $this->info('Error: ' . $pdoex->getMessage());
            die();
        } catch (ModelNotFoundException $mnfex) {
            $this->info('Error: ' . $mnfex->getMessage());
            die();
        }
    }
}
