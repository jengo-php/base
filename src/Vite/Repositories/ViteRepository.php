<?php

declare(strict_types=1);

namespace Jengo\Base\Vite\Repositories;

use Jengo\Base\Vite\Config\ViteConfig;
use Jengo\Base\Vite\ViteEntryPointScanner;

class ViteRepository
{
    public function __construct()
    {
        helper('Jengo\Base\Helpers\jengo');
    }

    protected string $cachePath = ROOTPATH . '.jengo/vite_entrypoints.json';

    public function getFullConfig(): ViteConfig
    {
        /**
         * @var ViteConfig $config
         */
        $config = config('ViteConfig');

        $config->entrypoints = array_unique([
            ...$this->loadEntrypoints(),
            ...$config->entrypoints
        ]);

        return $config;
    }

    protected function loadEntrypoints(): array
    {
        if (isProduction() && file_exists($this->cachePath)) {
            return json_decode(file_get_contents($this->cachePath), true) ?? [];
        }

        // In dev or if cache is missing, scan fresh
        return (new ViteEntryPointScanner())->scan();
    }

    protected function cacheEntrypoints(array $data): void
    {
        if (!is_dir(dirname($this->cachePath))) {
            mkdir(dirname($this->cachePath), 0755, true);
        }
        file_put_contents($this->cachePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function scan(): void
    {
        $config = $this->getFullConfig();

        $this->cacheEntrypoints($config->entrypoints);
    }
}