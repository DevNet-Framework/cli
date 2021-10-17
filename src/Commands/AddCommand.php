<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\Cli\ICommand;
use DevNet\System\Event\EventArgs;
use DevNet\System\Text\StringBuilder;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class AddCommand implements ICommand
{
    public function execute(object $sender, EventArgs $args): void
    {
        $namespace = 'Application';
        $className = null;
        $basePath  = null;
        $template  = $args->get('template');
        $help      = $args->get('--help');
        $name      = $args->get('--name');

        if ($help) {
            $this->showHelp();
        }

        if (!$template || !$template->Value) {
            Console::foreGroundColor(ConsoleColor::Red);
            Console::writeline('Template argument is missing!');
            Console::resetColor();
            exit;
        }

        if ($name) {
            $className = $name->Value;
        }

        $directory = $args->get('--directory');
        if ($directory) {
            $basePath = $directory->Value;
        }

        $templateName = $template->Value ?? '';
        $templateName = strtolower($templateName);
        $result       = null;

        switch ($templateName) {
            case 'class':
                $result = self::createClass($namespace, $className, $basePath);
                break;
            case 'controller':
                $result = self::createController($namespace, $className, $basePath);
                break;
            case 'entity':
                $result = self::createEntity($namespace, $className, $basePath);
                break;
            case 'migration':
                $result = self::createMigration($namespace, $className, $basePath);
                break;
            default:
                Console::foreGroundColor(ConsoleColor::Red);
                Console::writeline("The template {$templateName} not exist!");
                Console::resetColor();
                exit;
                break;
        }

        if (!$result) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Somthing whent wrong! faild to create {$className} class.");
            Console::resetColor();
            exit;
        }

        Console::foregroundColor(ConsoleColor::Green);
        Console::writeline("The {$templateName} {$className} was created successfully.");
        Console::resetColor();

        exit;
    }

    public static function createClass(string $namespace, ?string $className, ?string $basePath): bool
    {
        $destination = implode('/', [getcwd(), $basePath]);
        $namespace   = implode('\\', [$namespace, $basePath]);
        $namespace   = str_replace('/', '\\', $namespace);
        $namespace   = rtrim($namespace, '\\');
        $namespace   = ucwords($namespace, '\\');
        $className   = $className ?? 'MyClass';
        $className   = ucfirst($className);

        $context = new StringBuilder();
        $context->appendLine('<?php');
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine('use DevNet\System\Collections\ArrayList;');
        $context->appendLine('use DevNet\System\Linq;');
        $context->appendLine();
        $context->appendLine("class {$className}");
        $context->appendLine('{');
        $context->appendLine('    public function __construct()');
        $context->appendLine('    {');
        $context->appendLine('        // code...');
        $context->appendLine('    }');
        $context->appendLine('}');

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $myfile = fopen($destination . '/' . $className . '.php', 'w');
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if (!$size || !$status) {
            return false;
        }

        return true;
    }

    public static function createController(string $namespace, ?string $className, ?string $basePath): bool
    {
        $basePath    = $basePath ?? 'Controllers';
        $namespace   = implode('\\', [$namespace, $basePath]);
        $namespace   = str_replace('/', '\\', $namespace);
        $namespace   = rtrim($namespace, '\\');
        $namespace   = ucwords($namespace, '\\');
        $className   = $className ?? 'MyController';
        $className   = ucfirst($className);
        $destination = implode('/', [getcwd(), $basePath]);

        $context = new StringBuilder();
        $context->appendLine('<?php');
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine('use DevNet\Core\Controller\AbstractController;');
        $context->appendLine('use DevNet\Core\Controller\IActionResult;');
        $context->appendLine();
        $context->appendLine("class {$className} extends AbstractController");
        $context->appendLine('{');
        $context->appendLine('    public function index() : IActionResult');
        $context->appendLine('    {');
        $context->appendLine('        return $this->view();');
        $context->appendLine('    }');
        $context->appendLine('}');

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $myfile = fopen($destination . '/' . $className . '.php', 'w');
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if ($size && $status) {
            return true;
        }

        return false;
    }

    public static function createEntity(string $namespace, ?string $className, ?string $basePath): bool
    {
        $basePath    = $basePath ?? 'Models';
        $destination = implode('/', [getcwd(), $basePath]);
        $namespace   = implode('\\', [$namespace, $basePath]);
        $namespace   = str_replace('/', '\\', $namespace);
        $namespace   = rtrim($namespace, '\\');
        $namespace   = ucwords($namespace, '\\');
        $className   = $className ?? 'MyEntity';
        $className   = ucfirst($className);

        $context = new StringBuilder();
        $context->appendLine('<?php');
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine('use DevNet\Entity\IEntity;');
        $context->appendLine();
        $context->appendLine("class {$className} implements IEntity");
        $context->appendLine('{');
        $context->appendLine('    private int $Id;');
        $context->appendLine();
        $context->appendLine('    public function __get(string $name)');
        $context->appendLine('    {');
        $context->appendLine('        return $this->$name;');
        $context->appendLine('    }');
        $context->appendLine();
        $context->appendLine('    public function __set(string $name, $value)');
        $context->appendLine('    {');
        $context->appendLine('        if (!property_exists($this, $name))');
        $context->appendLine('        {');
        $context->appendLine('            throw new \Exception("The property {$name} doesn\'t exist.");');
        $context->appendLine('        }');
        $context->appendLine('        $this->$name = $value;');
        $context->appendLine('    }');
        $context->appendLine('}');

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $myfile = fopen($destination . '/' . $className . '.php', 'w');
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if ($size && $status) {
            return true;
        }

        return false;
    }

    public static function createMigration(string $namespace, ?string $className, ?string $basePath): bool
    {
        $basePath    = $basePath ?? 'Migrations';
        $destination = implode('/', [getcwd(), $basePath]);
        $namespace   = implode('\\', [$namespace, $basePath]);
        $namespace   = str_replace('/', '\\', $namespace);
        $namespace   = rtrim($namespace, '\\');
        $namespace   = ucwords($namespace, '\\');
        $className   = $className ?? 'MyMigration';
        $className   = ucfirst($className);

        $context = new StringBuilder();
        $context->appendLine('<?php');
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine('use DevNet\Entity\Migration\AbstractMigration;');
        $context->appendLine('use DevNet\Entity\Migration\MigrationBuilder;');
        $context->appendLine();
        $context->appendLine("class {$className} extends AbstractMigration");
        $context->appendLine('{');
        $context->appendLine('    public function up(MigrationBuilder $builder): void');
        $context->appendLine('    {');
        $context->appendLine('        $builder->createTable(\'MyTable\', function ($table) {;');
        $context->appendLine('            $table->column(\'Id\')->type(\'integer\')->nullable(false)->identity();');
        $context->appendLine('            $table->primaryKey(\'Id\');');
        $context->appendLine('        });');
        $context->appendLine('    }');
        $context->appendLine();
        $context->appendLine('    public function down(MigrationBuilder $builder): void');
        $context->appendLine('    {');
        $context->appendLine('        $builder->dropTable(\'MyTable\');');
        $context->appendLine('    }');
        $context->appendLine('}');

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $myfile = fopen($destination . '/' . date('Ymdhis') . '_' . $className . '.php', 'w');
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if ($size && $status) {
            return true;
        }

        return false;
    }

    public function showHelp()
    {
        Console::writeline('Usage: devnet new [template] [arguments] [options]');
        Console::writeline();
        Console::writeline('Options:');
        Console::writeline('  --help     Displays help for this command.');
        Console::writeline('  --project  Location to place the generated project.');
        Console::writeline();
        Console::writeline('templates:');
        Console::writeline('  class       Simple Class');
        Console::writeline('  controller  Controller Class');
        Console::writeline('  entity      Entity Class');
        Console::writeline('  migration   Migration Class');
        Console::writeline();
        exit;
    }
}
