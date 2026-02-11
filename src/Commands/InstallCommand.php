<?php

declare(strict_types=1);

namespace Jengo\Base\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Jengo\Base\Installers\Libraries\InstallerRunner;
use Jengo\Base\Installers\Libraries\InstallerTracker;
use Jengo\Base\Installers\Repositories\InstallerRepository;

class InstallCommand extends BaseCommand
{
    private InstallerRunner $runner;
    protected $group = 'Jengo';
    protected $name = 'jengo:install';
    protected $description = 'Run Jengo installers';
    protected $usage = 'jengo:install <installers> <options>';

    protected $arguments = [
        'installers' => 'Installers to run or skip'
    ];

    protected $options = [
        '--show' => 'List all installers',
        '--status' => 'Show install status',
        '--no-check' => 'Skip tracker',
        '--except' => 'Run all installers except the specified. Separate them with commas i.e vite,maizzle',
    ];

    public function run(array $params)
    {
        $isNoCheck = CLI::getOption('no-check') !== null;
        $isShow = CLI::getOption('show') !== null;
        $isStatus = CLI::getOption('status') !== null;

        $exceptOption = CLI::getOption('except');
        $except = $exceptOption
            ? array_filter(array_map('trim', explode(',', $exceptOption)))
            : [];

        $ranInstallers = [];

        if ($isNoCheck) {
            CLI::write(
                'WARNING: --no-check disables installer tracking and may overwrite files.',
                'yellow'
            );

            if (CLI::prompt('Continue?', ['y', 'n']) !== 'y') {
                CLI::newLine();
                CLI::write('Aborted.', 'light_gray');
                return;
            }
        }

        if ($isShow) {
            foreach (InstallerRepository::all() as $installer) {
                CLI::write(sprintf(
                    '%-12s %s',
                    $installer::name(),
                    $installer::description()
                ));
            }
            return;
        }

        if ($isStatus) {
            $tracker = new InstallerTracker();

            foreach (InstallerRepository::all() as $installer) {
                $installed = $tracker->isInstalled($installer::name());

                CLI::write(
                    sprintf('%-12s %s', $installer::name(), $installed ? '✔ installed' : '⏳ pending'),
                    $installed ? 'green' : 'yellow'
                );
            }

            return;
        }

        $this->runner = new InstallerRunner(null, $isNoCheck);

        // Handle --except
        if ($except) {
            foreach (InstallerRepository::all() as $installer) {
                $name = $installer::name();
                if (in_array($name, $except, true)) {
                    continue;
                }

                $ranInstallers[] = $name;
                $this->runner->run($installer);
            }

            $this->showSuccess($ranInstallers, $except);
            return;
        }

        $installers = [];

        foreach ($params as $option => $name) {
            // skip indices and any null option values
            if (!$name) {
                continue;
            }

            // skip any options
            if (!is_int($option) && CLI::getOption($option)) {
                continue;
            }

            $installers[] = $name;
        }

        // Handle explicit installer names
        if ($installers) {
            foreach ($installers as $name) {
                $installer = InstallerRepository::find($name);

                if (!$installer) {
                    CLI::error("Installer [$name] not found.");
                    continue;
                }

                $ranInstallers[] = $name;
                $this->runner->run($installer);
            }

            $this->showSuccess($ranInstallers, $except);
            return;
        }

        CLI::write('WARNING: This will run all installers in this project', 'yellow');
        CLI::newLine();

        if (CLI::prompt('Continue?', ['y', 'n']) !== 'y') {
            CLI::write('Aborted.', 'light_gray');
            return;
        }

        foreach (InstallerRepository::all() as $installer) {
            $ranInstallers[] = $installer::name();

            $this->runner->run($installer);
        }

        $this->showSuccess($ranInstallers, $except);
    }

    public function showSuccess(array $ran, array $skipped): void
    {
        $runnerReport = $this->runner->report();

        $ran = array_unique(
            [
                ...array_diff($ran, $runnerReport['skipped']),
                ...$runnerReport['ran']
            ],
        );

        $skipped = array_unique([
            ...$skipped,
            ...$runnerReport['skipped']
        ]);

        sort($skipped);
        sort($ran);

        $ranStr = $ran ? implode(', ', $ran) : 'None';
        $skippedStr = $skipped ? implode(', ', $skipped) : 'None';

        CLI::newLine();

        CLI::write('✔ Installed:', 'yellow');
        CLI::write($ranStr);

        CLI::newLine();
        CLI::write('⏭ Skipped:', 'yellow');
        CLI::write($skippedStr);

        CLI::newLine();
        CLI::write('Installers run successfully!', 'green');
    }
}
