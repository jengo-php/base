<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Jengo\Base\Installers\Libraries\InstallerRunner;
use Jengo\Base\Installers\Libraries\InstallerTracker;
use Tests\Support\Installers\FakeInstaller;

final class InstallerTest extends CIUnitTestCase
{
    public function testPublishCopiesFiles()
    {
        $installer = new FakeInstaller();

        $installer->install();

        $this->assertFileExists(
            ROOTPATH . 'vite-test/vite.config.js'
        );
    }

    public function testEnvUpdatesExistingKey()
    {
        file_put_contents(ROOTPATH . '.env', "FOO=bar\n");

        $installer = new FakeInstaller();

        $installer->install();

        $this->assertStringContainsString(
            'FOO=baz',
            file_get_contents(ROOTPATH . '.env')
        );
    }

    public function testEnvAppendsMissingKey()
    {
        file_put_contents(ROOTPATH . '.env', "FOO=bar\n");

        $installer = new FakeInstaller();

        $installer->install();

        $this->assertStringContainsString(
            'BAR=baz',
            file_get_contents(ROOTPATH . '.env')
        );
    }

    public function testInstallerIsMarkedInstalled()
    {
        $path = TESTPATH . 'installers.php';

        $tracker = new InstallerTracker($path);

        $this->assertFalse($tracker->isInstalled('vite'));

        $tracker->markInstalled('vite');

        $this->assertTrue($tracker->isInstalled('vite'));

        unlink($path);
    }

    public function testRunnerSkipsInstalledInstaller()
    {
        $path = TESTPATH . 'installers.php';

        $tracker = new InstallerTracker($path);

        $installer = new FakeInstaller('vite');
        $runner = new InstallerRunner($tracker);

        $runner->run($installer);
        $runner->run($installer);

        $this->assertSame(1, $installer->runs);

        unlink($path);
    }
}
