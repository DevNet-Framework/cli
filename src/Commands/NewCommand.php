<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\System\Command\CommandArgument;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\CommandOption;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\Text\StringBuilder;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class NewCommand extends CommandLine implements ICommandHandler
{
    public function __construct()
    {
        $this->setName('new');
        $this->setDescription('Create a new project.');
        $this->addArgument(new CommandArgument('template'));
        $this->addOption(new CommandOption('--help', '-h'));
        $this->addOption(new CommandOption('--project', '-p'));
        $this->addHandler($this);
    }

    public function execute(object $sender, CommandEventArgs $args): void
    {
        $namespace = "Application";
        $className = "Program";
        $basePath  = null;
        $template  = $args->get('template');
        $help      = $args->get('--help');
        $project   = $args->get('--project');

        if ($help) {
            $this->showHelp();
        }

        if (!$template || !$template->Value) {
            Console::foreGroundColor(ConsoleColor::Red);
            Console::writeline("Template argument is missing!");
            Console::resetColor();
            exit;
        }

        if ($project) {
            $basePath = $project->Value;
        }

        $destination  = implode("/", [getcwd(), $basePath]);
        $templateName = $template->Value ?? '';
        $templateName = strtolower($templateName);
        $result       = false;

        if ($templateName == 'console') {
            $result = self::createProject($namespace, $className, $destination);
        } else {
            $rootDir = dirname(__DIR__, 3);
            $source  = $rootDir . '/templates/' . $templateName;
            if (!is_dir($source)) {
                Console::foregroundColor(ConsoleColor::Red);
                Console::writeline("The template {$templateName} does not exist!");
                Console::resetColor();
                exit;
            }

            $result  = self::copyProject($source, $destination);
        }

        if ($result) {
            Console::foregroundColor(ConsoleColor::Green);
            Console::writeline("The {$templateName} template was created successfully.");
            Console::resetColor();
        } else {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Somthing whent wrong! faild to create {$templateName} template.");
            Console::resetColor();
        }

        exit;
    }

    public static function createProject(string $namespace, string $className, string $destination): bool
    {
        $namespace = ucwords($namespace, "\\");
        $className = ucfirst($className);

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $context = new StringBuilder();
        $context->appendLine("<?php");
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine("use DevNet\System\IO\Console;");
        $context->appendLine();
        $context->appendLine("class {$className}");
        $context->appendLine("{");
        $context->appendLine("    public static function main(array \$args = [])");
        $context->appendLine("    {");
        $context->appendLine("        Console::writeline(\"Hello World!\");");
        $context->appendLine("    }");
        $context->appendLine("}");

        $myfile = fopen($destination . "/" . $className . ".php", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if (!$size || !$status) {
            return false;
        }

        $context = new StringBuilder();
        $context->appendLine("<?xml version=\"1.0\"?>");
        $context->appendLine("<project>");
        $context->appendLine("  <properties>");
        $context->appendLine("    <namespace>{$namespace}</namespace>");
        $context->appendLine("    <entrypoint>{$className}</entrypoint>");
        $context->appendLine("  </properties>");
        $context->appendLine("</project>");

        $myfile = fopen($destination . "/project.phproj", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if (!$size || !$status) {
            return false;
        }

        return true;
    }

    public static function copyProject(string $src, string $dst): bool
    {
        try {
            $dir = opendir($src);
            if (!is_dir($dst)) {
                mkdir($dst, 0777, true);
            }

            while ($file = readdir($dir)) {
                if ($file !== '.' && $file !== '..' && $file !== '.git') {
                    if (is_dir($src . '/' . $file)) {
                        self::copyProject($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
        } catch (\Throwable $th) {
            return false;
        }

        closedir($dir);

        return true;
    }

    public function showHelp()
    {
        Console::writeline("Usage: devnet new [options]");
        Console::writeline("Usage: devnet new [template] [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help     Displays help for this command.");
        Console::writeline("  --project  Location to place the generated project.");
        Console::writeline();
        Console::writeline("templates:");

        $root = dirname(__DIR__, 3);
        $list = [];

        if (is_dir($root . '/templates')) {
            $list = scandir($root . '/templates');
        }

        $metadata[] = ['name' => 'console', 'description' => 'Console Applicatinon project'];
        $maxLenth   = 7; // the initial max length is the length the word "console"

        foreach ($list as $name) {
            if (file_exists($root . '/templates/' . $name . '/composer.json')) {
                $json    = file_get_contents($root . '/templates/' . $name . '/composer.json');
                $project = json_decode($json);

                $lenth   = strlen($name);
                if ($lenth > $maxLenth) {
                    $maxLenth = $lenth;
                }

                $metadata[] = ['name' => $name, 'description' => $project->description];
            }
        }

        //print template description with auto-alignment
        foreach ($metadata as $template) {
            $lenth = strlen($template['name']);
            $steps = $maxLenth - $lenth + 4;
            $space = str_repeat(" ", $steps);
            Console::writeline("  {$template['name']}{$space}{$template['description']}");
        }

        Console::writeline();
        exit;
    }
}
