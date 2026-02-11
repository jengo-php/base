<?php

declare(strict_types=1);

namespace Tests;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\Test\Mock\MockInputOutput;
use Config\Database;
use Jengo\Base\Installers\Libraries\InstallerTracker;
use Tests\Support\Models\UserModel;
use function PHPUnit\Framework\assertTrue;

class BaseTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = null;

    private MockInputOutput $io;

    protected function setUp(): void
    {
        parent::setUp();

        $this->io = new MockInputOutput();

        CLI::setInputOutput($this->io);

        $this->loadDependencies();
        $this->migrateDatabase();
        $this->generateData();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        CLI::resetInputOutput();

        $this->regressDatabase();
        $this->migrateDatabase();
    }

    public function testMakeEventCommand(): void
    {
        command('make:event example App');

        $dir = APPPATH . "Events";
        $path = "$dir/ExampleEvent.php";

        assertTrue(file_exists($path));

        if (file_exists($path)) {
            unlink($path);
        }

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    public function testMakeLayoutCommand(): void
    {
        command('make:layout example --layout app');

        $dir = APPPATH . "Views/layouts";
        $path = "$dir/example.layout.php";

        assertTrue(file_exists($path));

        if (file_exists($path)) {
            unlink($path);
        }

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    public function testMakeBaseLayoutCommand(): void
    {
        command('make:layout base --base name');

        $dir = APPPATH . "Views/layouts";
        $path = "$dir/base.layout.php";

        assertTrue(file_exists($path));

        if (file_exists($path)) {
            unlink($path);
        }

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    public function testMakePageCommand(): void
    {
        command('make:page user');

        $dir = APPPATH . "Views/pages";
        $path = "$dir/user.page.php";

        assertTrue(file_exists($path));

        if (file_exists($path)) {
            unlink($path);
        }

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }

    public function testSetupCommand(): void
    {
        $this->io->setInputs([
            'y',
        ]);

        command('jengo:setup');

        $basePath = APPPATH . 'Views';
        $layoutsPath = "$basePath/layouts";
        $pagesPath = "$basePath/pages";
        $partialsPath = "$layoutsPath/partials";

        $dirs = [
            'partials' => [
                'path' => $partialsPath,
                'files' => [
                    'footer.layout.partial',
                    'header.layout.partial',
                ]
            ],

            'layouts' => [
                'path' => $layoutsPath,
                'files' => [
                    'app.layout',
                    'base.layout'
                ]
            ],

            'pages' => [
                'path' => $pagesPath,
                'files' => [
                    'home.page',
                ]
            ],
        ];

        $this->assertFileExists("$partialsPath/" . $dirs['partials']['files'][0] . ".php");
        $this->assertFileExists("$partialsPath/" . $dirs['partials']['files'][1] . ".php");
        $this->assertFileExists("$layoutsPath/" . $dirs['layouts']['files'][0] . ".php");
        $this->assertFileExists("$layoutsPath/" . $dirs['layouts']['files'][1] . ".php");
        $this->assertFileExists("$pagesPath/" . $dirs['pages']['files'][0] . ".php");

        foreach ($dirs as $dir) {
            $path = $dir['path'];
            foreach ($dir['files'] as $file) {
                $cpath = "$path/$file.php";
                if (file_exists($cpath)) {
                    unlink($cpath);
                }
            }

            if (is_dir($path)) {
                rmdir($path);
            }
        }
    }

    public function testModelFacade(): void
    {
        helper('Jengo\Base\Helpers\jengo');

        $users = model_of(UserModel::class)::findAll();

        $this->assertIsArray($users);
    }

    private function generateData(): void
    {
        $db = Database::connect('tests');
        $users = (new Fabricator(UserModel::class))->make(10);

        $db->table('users')->insertBatch($users);
    }
}