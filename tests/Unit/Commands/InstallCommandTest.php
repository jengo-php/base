<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\Mock\MockInputOutput;
use Jengo\Base\Installers\Libraries\InstallerTracker;

final class InstallCommandTest extends CIUnitTestCase
{
    private MockInputOutput $io;

    protected function setUp(): void
    {
        parent::setUp();

        $this->io = new MockInputOutput();

        $this->cleanFileSystem();

        CLI::setInputOutput($this->io);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $out = $this->io->getOutput();

        CLI::resetInputOutput();

        $this->cleanFileSystem();

        var_dump($out);
    }
    public function testCommand(): void
    {
        $this->io->setInputs([
            'y'
        ]);

        command('jengo:install vite');

        $tracker = new InstallerTracker();

        $this->assertTrue($tracker->isInstalled('vite'));
    }

    private function cleanFileSystem(): void
    {
        $baseDir = ROOTPATH;
        $configDir = "$baseDir.jengo";

        $files = [
            'package.json',
            'vite.config.js',
        ];

        helper('filesystem');

        if (is_dir($configDir)) {
            delete_files($configDir);

            rmdir($configDir);
        }

        foreach ($files as $file) {
            $path = "$baseDir$file";
            if(file_exists($path)) {
                unlink($path);
            }
        }
    }
}
