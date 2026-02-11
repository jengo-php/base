<?php

declare(strict_types=1);

namespace Jengo\Base\Vite\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Jengo\Base\Vite\Repositories\ViteRepository;

class ViteConfigCommand extends BaseCommand
{
    protected $group       = 'Jengo';
    protected $name        = 'vite:config';
    protected $description = 'Returns the Vite entrypoint configuration as JSON.';

    public function run(array $params)
    {
        $repo = new ViteRepository();
        $config = $repo->getFullConfig();

        // Output raw JSON so the JS plugin can parse it
        $json = json_encode($config->toArray());

        CLI::write($json);
    }
}