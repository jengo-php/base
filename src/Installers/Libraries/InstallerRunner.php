<?php 

declare(strict_types=1);

namespace Jengo\Base\Installers\Libraries;

use CodeIgniter\CLI\CLI;
use Jengo\Base\Installers\Contracts\InstallerInterface;

class InstallerRunner
{
    protected InstallerTracker $tracker;
    protected bool $skipTracking = false;

    protected array $skipped = [];
    protected array $ran = [];

    public function __construct(
        ?InstallerTracker $tracker = null,
        bool $skipTracking = false
    ) {
        $this->tracker = $tracker ?? new InstallerTracker();
        $this->skipTracking = $skipTracking;
    }

    public function run(InstallerInterface $installer): void
    {
        $name = $installer::name();

        if (! $this->skipTracking && $this->tracker->isInstalled($name)) {
            $this->skipped[] = $name;
            CLI::write("Skipping [$name]...", 'yellow');
            CLI::write("This installer has already been run.", 'light_yellow');
            return;
        }

        if (! $installer->shouldRun()) {
            $this->skipped[] = $name;
            CLI::write("Skipping [$name]...", 'yellow');
            CLI::write($installer::reasonForSkipping(), 'light_yellow');
            return;
        }

        CLI::newLine();
        CLI::write("Installing [$name]...", 'purple');

        $installer->install();

        if (! $this->skipTracking) {
            $this->tracker->markInstalled($name);
        }

        $this->ran[] = $name;
    }

    public function report(): array
    {
        return [
            'ran' => array_unique($this->ran),
            'skipped' => array_unique($this->skipped),
        ];
    }
}
