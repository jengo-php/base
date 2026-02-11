<?php

declare(strict_types=1);

namespace Jengo\Base\Vite;

use Jengo\Base\Vite\Config\ViteConfig;

class ViteEntryPointScanner
{
    protected array $searchPaths = [
        APPPATH
    ];

    public function scan(): array
    {
        $config = config(ViteConfig::class);

        $this->searchPaths = [
            ...$this->searchPaths,
            ...$config->searchPaths
        ];

        $entrypoints = [];
        foreach ($this->searchPaths as $path) {
            if(!is_dir($path)) {
                continue;
            }

            $directory = new \RecursiveDirectoryIterator($path);
            $iterator = new \RecursiveIteratorIterator($directory);

            foreach ($iterator as $file) {
                // Match: something.entrypoint.ts or something.entrypoint.scss
                if (preg_match('/^(.+)\.entrypoint\.(ts|js|scss|css)$/', $file->getFilename())) {
                    // Create a relative path from ROOTPATH for Vite to consume
                    $relativePath = str_replace(ROOTPATH, '', $file->getRealPath());
                    $entrypoints[] = $relativePath;
                }
            }
        }
        return $entrypoints;
    }
}