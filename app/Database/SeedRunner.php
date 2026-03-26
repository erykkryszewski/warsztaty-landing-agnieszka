<?php

declare(strict_types=1);

namespace App\Database;

use App\Core\Application;
use Database\Seeders\DatabaseSeeder;

class SeedRunner
{
    public function run(Application $app): void
    {
        (new DatabaseSeeder())->run($app);
    }
}
