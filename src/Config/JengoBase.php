<?php 

declare(strict_types=1);

namespace Jengo\Base\Config;

use CodeIgniter\Config\BaseConfig;
use Jengo\Base\Installers\PageSystemInstaller;
use Jengo\Base\Installers\ViteInstaller;

class JengoBase extends BaseConfig
{
    /**
     * List of installer class names.
     *
     * @var class-string<\Jengo\Base\Installers\Contracts\InstallerInterface>[]
     */
    public array $installers = [
        ViteInstaller::class,
        PageSystemInstaller::class,
    ];
}
