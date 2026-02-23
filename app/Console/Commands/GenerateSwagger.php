<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use L5Swagger\Generator;

class GenerateSwagger extends Command
{
    protected $signature = 'swagger:generate-complete';
    protected $description = 'Generate complete Swagger documentation';

    public function handle(Generator $generator)
    {
        try {
            $generator->generateDocs();
            $this->info('Swagger documentation generated successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
