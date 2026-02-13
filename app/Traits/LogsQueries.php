<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait LogsQueries
{
    protected function enableQueryLog(): void
    {
        DB::enableQueryLog();
    }

    protected function logQueries(string $context = ''): void
    {
        $queries = DB::getQueryLog();
        
        Log::channel('daily')->info("Query Log - {$context}", [
            'total_queries' => count($queries),
            'queries' => $queries
        ]);

        DB::flushQueryLog();
    }

    protected function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }
}
