<?php

declare(strict_types=1);

namespace Jengo\Base\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Files\Exceptions\FileException;
use Config\Autoload;

/**
 * Creates a new Event Class extending App\Events\AbstractEvent.
 * The class will be placed in the specified namespace's 'Events' subdirectory.
 */
class MakeEventCommand extends BaseCommand
{
    /**
     * @var string The group the command is lumped under
     */
    protected $group = 'Jengo';

    /**
     * @var string The command's name
     */
    protected $name = 'make:event';

    /**
     * @var string The command's short description
     */
    protected $description = 'Creates a new Event Class file based on AbstractEvent structure.';

    /**
     * @var array The command's usage
     */
    protected $usage = 'make:event <name> <namespace>';

    /**
     * @var array The command's arguments
     */
    protected $arguments = [
        'name' => 'The Event Class name (e.g., UserCreated).',
        'namespace' => 'The root application namespace (e.g., App\Auth or App\User).',
    ];

    /**
     * @var array The command's options
     */
    protected $options = [];

    /**
     * Actually execute the command.
     */
    public function run(array $params)
    {
        // 1. Get arguments
        $className = array_shift($params);
        $baseNamespace = array_shift($params);

        if (empty($className)) {
            $className = CLI::prompt('Event Class Name (e.g., OrderPlaced):', null, 'required');
        }
        
        if (empty($baseNamespace)) {
            $baseNamespace = CLI::prompt('Base App Namespace (e.g., App\\Auth):', 'App\\Auth', 'required');
        }

        // 2. Normalize Names
        $className = str_replace(['-'], ' ', $className);
        $className = str_replace(' ', '', ucwords($className));

        if (!str_ends_with(strtolower($className), 'event')) {
            $className .= 'Event';
        }

        // Ensure namespace uses backslashes
        $baseNamespace = trim($baseNamespace, '\\');

        // Final full namespace
        $fullNamespace = $baseNamespace . '\\Events';

        // 3. Determine the file path
        /** @var Autoload $config */
        $config = config(Autoload::class);
        $mapping = $config->psr4;

        $newMapping = [];
        foreach ($mapping as $key => $value) {
            // Normalize keys to not have trailing backslashes
            $normalizedKey = trim($key, '\\');

            $newMapping[$normalizedKey] = $value;
        }

        $mapping = $newMapping;

        if (!isset($mapping[$baseNamespace])) {
            CLI::error("Error: The namespace '{$baseNamespace}' is not defined in your Autoload config (PSR-4).");
            return;
        }

        $basePath = rtrim($mapping[$baseNamespace], '/') . '/';
        $targetDir = $basePath . 'Events/';
        $filePath = $targetDir . $className . '.php';

        if (is_file($filePath)) {
            CLI::error("Error: Event file already exists at {$filePath}");
            return;
        }

        // 4. Generate the template content
        $content = $this->getTemplate($fullNamespace, $className);

        // 5. Write the file
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        try {
            if (file_put_contents($filePath, $content) === false) {
                throw new FileException('Failed to write file.');
            }

            CLI::write("Event created successfully!", 'green');
            CLI::write("Path: " . $filePath, 'yellow');
            CLI::write("Class: " . $fullNamespace . '\\' . $className, 'yellow');
            CLI::write("Constant: " . strtolower($baseNamespace) . '.' . strtolower($className));

        } catch (\Throwable $e) {
            CLI::error("An error occurred while writing the file: " . $e->getMessage());
        }
    }

    /**
     * Generates the boilerplate content for the Event class.
     */
    private function getTemplate(string $namespace, string $className): string
    {
        // Generate a consistent, lower-cased event name for the constant
        $constantName = str_replace('\\', '.', strtolower(str_replace('event', '', $namespace . '\\' . $className)));
        $constantName = str_replace('app.events', 'app', $constantName); // Clean up common prefix

        return <<<EOT
<?php

declare(strict_types=1);

namespace {$namespace};

use Jengo\Base\Events\AbstractEvent;

/**
 * Event triggered on {$className}.
 */
final class {$className} extends AbstractEvent
{
    // The unique name for this event.
    public const string NAME = '{$constantName}';

    /**
     * Executes the tasks required when this event is triggered.
     *
     * @param mixed ...\$args The data payload associated with the event.
     */
    public static function event(mixed ...\$args): void
    {
        // Example: Log the event details
        // log_message('info', 'Event: {$constantName} triggered with payload: ' . json_encode(\$args));

        // Add your specific event logic here.
        // E.g., Send an email, update a counter, dispatch a job.
    }
}
EOT;
    }
}