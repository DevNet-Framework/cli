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
        if ($args->Residual) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("The specified argument or option is not valid, try '--help' option for usage information.");
            Console::resetColor();
            exit;
        }

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
            if (!$project->Value) {
                Console::foreGroundColor(ConsoleColor::Red);
                Console::writeline('Project argument is missing!');
                Console::resetColor();
                exit;
            }
            $basePath = $project->Value;
        }

        $destination  = implode("/", [getcwd(), $basePath]);
        $templateName = $template->Value ?? '';
        $templateName = strtolower($templateName);

        $rootDir = dirname(__DIR__, 3);
        $source  = $rootDir . '/templates/' . $templateName;
        $result  = false;

        if ($templateName == 'console' || $templateName == 'web' || $templateName == 'mvc') {
            $source .= '-template';
        }
        if (!is_dir($source)) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("The template {$templateName} does not exist!");
            Console::resetColor();
            exit;
        }

        $result = self::createProject($source, $destination);

        if ($result) {
            Console::foregroundColor(ConsoleColor::Green);
            Console::writeline("The {$templateName} project was created successfully.");
            Console::resetColor();
        } else {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Somthing whent wrong! faild to create {$templateName} template.");
            Console::resetColor();
        }

        exit;
    }

    public static function createProject(string $src, string $dst): bool
    {
        try {
            $dir = opendir($src);
            if (!is_dir($dst)) {
                mkdir($dst, 0777, true);
            }

            while ($file = readdir($dir)) {
                if ($file !== '.' && $file !== '..' && $file !== '.git') {
                    if (is_dir($src . '/' . $file)) {
                        self::createProject($src . '/' . $file, $dst . '/' . $file);
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
        Console::writeline("Usage: devnet new [template] [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help     Displays help for this command.");
        Console::writeline("  --project  Location of where to place the template project.");
        Console::writeline();
        Console::writeline("templates:");

        $root = dirname(__DIR__, 3);
        $list = [];

        if (is_dir($root . '/templates')) {
            $list = scandir($root . '/templates');
        }

        // remove current and back directory references (. , ..)
        array_shift($list);
        array_shift($list);

        $maxLenth = 0;
        $metadata = [];
        foreach ($list as $name) {
            if (file_exists($root . '/templates/' . $name . '/composer.json')) {
                $json    = file_get_contents($root . '/templates/' . $name . '/composer.json');
                $project = json_decode($json);

                if ($name == 'console-template' || $name == 'web-template' || $name == 'mvc-template') {
                    $name = strstr($name, '-', true);
                }

                $lenth = strlen($name);
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
