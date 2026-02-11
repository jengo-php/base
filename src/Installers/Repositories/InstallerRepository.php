<?php

declare(strict_types=1);

namespace Jengo\Base\Installers\Repositories;

use CodeIgniter\CLI\CLI;
use Jengo\Base\Config\JengoBase;
use Jengo\Base\Installers\Contracts\InstallerInterface;
use RuntimeException;

class InstallerRepository
{
    /** @return InstallerInterface[] */
    public static function all(): array
    {
        $config = config(JengoBase::class);

        $installers = [];

        foreach ($config->installers as $class) {
            if (!is_subclass_of($class, InstallerInterface::class)) {
                throw new RuntimeException(
                    "{$class} must implement InstallerInterface"
                );
            }

            $installers[] = new $class();
        }

        return $installers;
    }

    public static function find(string $name): ?InstallerInterface
    {
        foreach (self::all() as $installer) {
            if ($installer::name() === $name) {
                return $installer;
            }
        }

        return null;
    }
}
