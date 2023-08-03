<?php

namespace App\Console\Commands;

use App\Factories\LinkFactory;
use App\Interfaces\FindableLink;
use App\Services\CrawlerService;
use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use PDOException;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl {--env=} {--flush}';

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
        $echo = (bool)$this->option('verbose');
        $flushData = (bool)$this->option('flush');
        $finder = $this->getLinkFinder($env);

        $file = Storage::path($env . '.csv');
        $contents = FileService::toMap($file, ['blog_id', 'url']);

        if ($flushData) {
            $message = 'The --flush option will truncate the ' . $finder->getTable() . ' table' . PHP_EOL;
            if (! $this->confirm($message . ' Do you wish to continue?', false)) {
                $this->info("Process terminated by user");

                return Command::FAILURE;
            }

            $finder->truncate();
            echo $finder->getTable() . ' flushed.' . PHP_EOL;
        }

        (new CrawlerService())->setEnvironment($finder)
            ->verbose($echo)
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
