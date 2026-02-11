<?php 

declare(strict_types=1);

namespace Jengo\Base\Installers\Libraries;

class InstallerTracker
{
    protected string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? ROOTPATH . '.jengo/installers.php';
    }

    public function all(): array
    {
        if (! file_exists($this->path)) {
            return [];
        }

        return require $this->path;
    }

    public function isInstalled(string $name): bool
    {
        return ($this->all()[$name]['installed'] ?? false) === true;
    }

    public function markInstalled(string $name): void
    {
        $data = $this->all();

        $data[$name] = [
            'installed'    => true,
            'installed_at' => gmdate('c'),
        ];

        $this->persist($data);
    }

    protected function persist(array $data): void
    {
        $dir = dirname($this->path);

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $export = var_export($data, true);

        $contents = <<<PHP
<?php

return {$export};

PHP;

        file_put_contents($this->path, $contents);
    }
}
