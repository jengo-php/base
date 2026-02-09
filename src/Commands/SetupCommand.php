<?php

namespace Jengo\Base\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Test\Mock\MockInputOutput;

class SetupCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Jengo';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'jengo:setup';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Sets up the Jengo environment in your app';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'jengo:setup [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];
    /**
     * Creates a new token class file.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        // inform of destructive nature of the command

        $options = ['y', 'n'];

        $ans = CLI::prompt(
            'This is a destructive command, do you wish to continue?',
            $options,
            ["in_list[" . implode(',', $options) . "]"]
        );

        if (!in_array(strtolower($ans), ['y', 'yes'])) {
            CLI::newLine();
            CLI::write('Setup terminated successfully!', 'yellow');
            return;
        }

        // 1. setup the page system
        //  a. create partials
        $this->createPartials();

        //  b. create base layout
        $this->createBaseLayout();

        //  c. create app layout
        $this->createAppLayout();

        // d. create the home page
        $this->createHomePage();

        // e. replace home controller
        $this->editHomeController();

        // 2.  add the jengo helper to the autoload file

        CLI::newLine();
        CLI::write('Setup completed successfully!', 'green');
    }

    private function createPartials(): void
    {
        $dir = APPPATH . "Views/layouts/partials/";
        $files = [
            'header.layout.partial',
            'footer.layout.partial'
        ];

        $content = [
            'header.layout.partial' => '<!-- Header file - Use to add any tags in the head tag -->',
            'footer.layout.partial' => '<!-- Footer file - Use to add any links to be placed at the end of the body tag  -->'
        ];

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($files as $file) {
            $filename = "$dir{$file}.php";

            if (!file_exists($filename)) {
                file_put_contents($filename, $content[$file]);
            }
        }
    }

    private function createBaseLayout(): void
    {
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        command('make:layout base --base');

        CLI::resetInputOutput();
    }

    private function createAppLayout(): void
    {
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        command('make:layout app');

        CLI::resetInputOutput();
    }

    private function createHomePage(): void
    {
        $io = new MockInputOutput();

        CLI::setInputOutput($io);

        command('make:page home');

        CLI::resetInputOutput();
    }

    private function editHomeController(): void
    {
        $path = APPPATH . "Controllers/Home.php";
        $welcomePagePath = APPPATH . "Views/welcome_message.php";

        $content = "<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return page('home');
    }
}";


        if (file_exists($path)) {
            unlink($path);
        } 
        
        if (file_exists($welcomePagePath)) {
            unlink($welcomePagePath);
        }

        file_put_contents($path, $content);
    }
}
