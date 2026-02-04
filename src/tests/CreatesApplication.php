<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        try {
            $app->make(Kernel::class)->bootstrap();
        } catch (\Throwable $e) {
            echo "\nBOOTSTRAP ERROR: " . $e->getMessage() . "\n";
            echo "IN FILE: " . $e->getFile() . " ON LINE: " . $e->getLine() . "\n";
            echo $e->getTraceAsString() . "\n";
            exit(1);
        }

        return $app;
    }
}
