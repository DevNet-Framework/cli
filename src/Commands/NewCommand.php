<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Commands;

use DevNet\CLI\Plugin\TemplateProvider;
use DevNet\CLI\Plugin\TemplateRegistry;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class NewCommand extends CommandLine implements ICommandHandler
{
    private TemplateRegistry $registry;

    public function __construct()
    {
        parent::__construct('new', 'Create a new DevNet project');

        $this->addArgument('template', 'The template project want to create');
        $this->addOption('--output', 'Location to place the generated project output', '-o');

        $this->registry = TemplateRegistry::getSingleton();
        $this->registry->set('console', new TemplateProvider(dirname(__DIR__, 3) . '/console'));

        if (is_file(dirname(__DIR__, 3) . '/web/composer.json')) {
            $this->registry->set('web', new TemplateProvider(dirname(__DIR__, 3) . '/web'));
        }

        if (is_file(dirname(__DIR__, 3) . '/mvc/composer.json')) {
            $this->registry->set('mvc', new TemplateProvider(dirname(__DIR__, 3) . '/mvc'));
        }

        $this->setHelp(function ($builder) {
            $builder->useDefaults();
            $builder->writeHeading('Templates:');

            $rows = [];
            foreach ($this->registry as $provider) {
                $rows[$provider->getName()] = $provider->getDescription();
            }

            $builder->writeRows($rows);
        });
    }

    public function onExecute(object $sender, CommandEventArgs $args): void
    {
        $template = $args->get('template');
        if (!$template || !$template->Value) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("Template argument is missing!");
            Console::resetColor();
            return;
        }

        $path = null;
        $output = $args->get('--output');
        if ($output) {
            if (!$output->Value) {
                Console::$ForegroundColor = ConsoleColor::Red;
                Console::writeLine('Directory argument is missing!');
                Console::resetColor();
                return;
            }
            $path = $output->Value;
        }

        $destination  = implode("/", [getcwd(), $path]);
        $templateName = $template->Value ?? '';
        $templateName = strtolower($templateName);
        $provider     = $this->registry->get($templateName);

        if (!$provider || !is_dir($provider->getPath())) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("The template {$templateName} does not exist!");
            Console::resetColor();
            return;
        }

        $result = self::createProject($provider->getSourcePath(), $destination);

        if ($result) {
            Console::$ForegroundColor = ConsoleColor::Green;
            Console::writeLine("The template {$templateName} project was created successfully.");
            Console::resetColor();
        } else {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("Something went wrong! failed to create {$templateName} template.");
            Console::resetColor();
        }
    }

    public static function createProject(string $source, string $destination): bool
    {
        try {
            $dir = opendir($source);
            if (!is_dir($destination)) {
                mkdir($destination, 0777, true);
            }

            while ($file = readdir($dir)) {
                if ($file !== '.' && $file !== '..' && $file !== '.git') {
                    if (is_dir($source . '/' . $file)) {
                        self::createProject($source . '/' . $file, $destination . '/' . $file);
                    } else {
                        copy($source . '/' . $file, $destination . '/' . $file);
                    }
                }
            }
        } catch (\Throwable $th) {
            return false;
        }

        closedir($dir);

        return true;
    }
}
