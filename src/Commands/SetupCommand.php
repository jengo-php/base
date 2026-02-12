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

        // 2.  add the jengo helper to the autoload file
        $this->addHelperToAutoload();

        CLI::newLine();
        CLI::write('Setup completed successfully!', 'green');
    }

    private function addHelperToAutoload(): void
    {
        $path = APPPATH . 'Config/Autoload.php';
        $content = file_get_contents($path);
        
        // Check if helper is already added
        if (str_contains($content, 'Jengo\Base\Helpers\jengo')) {
            CLI::write('Jengo helper is already configured.', 'yellow');
            return;
        }

        // Find the helpers array and add the helper
        $pattern = '/(public\s+\$helpers\s*=\s*\[)(.*?)(\];)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            // Check if array is empty or has items to format correctly
            $currentHelpers = trim($matches[2]);
            $newHelper = "'Jengo\\\Base\\\Helpers\\\jengo'";
            
            if (empty($currentHelpers)) {
                $replacement = "public \$helpers = [\n        $newHelper,\n    ];";
            } else {
                // Remove trailing comma if present to avoid syntax error when appending, though PHP allows trailing commas.
                // Safest is to just prepend or append. 
                // Let's prepend to be safe ensuring it's in the list.
                $replacement = "public \$helpers = [\n        $newHelper,\n" . $matches[2] . "];";
            }
            
            // Actually, simpler regex replacement might be better to just Insert after opening bracket
            $content = preg_replace(
                '/(public\s+\$helpers\s*=\s*\[)/',
                "$1\n        'Jengo\\\Base\\\Helpers\\\jengo',",
                $content,
                1
            );
            
            file_put_contents($path, $content);
            CLI::write('Jengo helper added to Autoload config.', 'green');
        } else {
            CLI::error('Could not automatically add Jengo helper to Autoload.php. Please add it manually.');
        }
    }
}
