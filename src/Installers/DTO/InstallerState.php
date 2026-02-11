<?php 

declare(strict_types=1);

namespace Jengo\Base\Installers\DTO;

final class InstallerState
{
    public function __construct(
        public bool $installed,
        public ?string $installedAt = null
    ) {}
}
