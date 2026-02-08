<?php

declare(strict_types=1);

namespace Tests;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\Test\Mock\MockInputOutput;
use Config\Database;
use Tests\Support\Models\UserModel;
use function PHPUnit\Framework\assertTrue;

class BaseTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadDependencies();
        $this->migrateDatabase();
        $this->generateData();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->regressDatabase();
        $this->migrateDatabase();
    }

    public function testMakeEventCommand(): void
    {
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        $io->setInputs(['example', 'App']);

        command('make:event');

        CLI::resetInputOutput();

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
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        command('make:layout example --layout app');

        CLI::resetInputOutput();

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
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        command('make:layout base --base name');

        CLI::resetInputOutput();

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
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        command('make:page user');

        CLI::resetInputOutput();

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
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        command('jengo:setup');

        CLI::resetInputOutput();

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
        $users = new Fabricator(UserModel::class)->make(10);

        $db->table('users')->insertBatch($users);
    }
}
