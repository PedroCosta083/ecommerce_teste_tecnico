<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearAppCache extends Command
{
    protected $signature = 'cache:clear-app {--tag=* : Specific cache tags to clear}';
    protected $description = 'Clear application cache by tags';

    public function handle(): int
    {
        $tags = $this->option('tag');

        if (empty($tags)) {
            $this->info('Clearing all application cache...');
            Cache::tags(['products'])->flush();
            Cache::tags(['categories'])->flush();
            Cache::tags(['tags'])->flush();
            $this->info('All application cache cleared successfully!');
        } else {
            foreach ($tags as $tag) {
                $this->info("Clearing cache for tag: {$tag}");
                Cache::tags([$tag])->flush();
            }
            $this->info('Cache cleared successfully!');
        }

        return Command::SUCCESS;
    }
}
