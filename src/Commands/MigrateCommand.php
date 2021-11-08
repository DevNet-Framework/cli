<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\Core\Configuration\ConfigurationBuilder;
use DevNet\Core\Dependency\ServiceCollection;
use DevNet\Core\Dependency\ServiceProvider;
use DevNet\Entity\EntityContext;
use DevNet\Entity\EntityOptions;
use DevNet\Entity\Migration\Migrator;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\CommandOption;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\Runtime\LauncherProperties;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class MigrateCommand extends CommandLine implements ICommandHandler
{
    public function __construct()
    {
        $this->setName('migrate');
        $this->setDescription('Migrate database schema and data.');
        $this->addOption(new CommandOption ('--help', '-h'));
        $this->addOption(new CommandOption('--target'));
        $this->addHandler($this);
    }

    public function execute(object $sender, CommandEventArgs $args): void
    {
        $workspace = getcwd();
        $loader    = LauncherProperties::getLoader();
        $help      = $args->get('--help');
        $target    = $args->get('--target');

        if ($help) {
            $this->showHelp();
        }

        $projectFile = simplexml_load_file($workspace . "/project.phproj");

        if ($projectFile) {
            $namespace = $projectFile->properties->namespace;
            $entrypoint = $projectFile->properties->entrypoint;
            $packages  = $projectFile->dependencies->package ?? [];

            if ($namespace && $entrypoint) {
                $namespace  = (string)$namespace;
                $entrypoint = (string)$entrypoint;
                $loader->map($namespace, "/");
            }

            foreach ($packages as $package) {
                $include = (string)$package->attributes()->include;
                if (file_exists($workspace . '/' . $include)) {
                    require $workspace . '/' . $include;
                }
            }
        }

        $entity = null;
        $startupClass = $namespace . "\\Startup";

        if (class_exists($startupClass)) {
            $configBuilder = new ConfigurationBuilder();
            $configBuilder->addBasePath($workspace);
            $configBuilder->addJsonFile("/settings.json");

            $startup = new $startupClass($configBuilder->build());
            $services = new ServiceCollection();
            $startup->configureServices($services);

            $provider = new ServiceProvider($services);
            $entity = $provider->getService(EntityContext::class);
        } else {
            $models = (file_exists($workspace . '/Models')) ? scandir($workspace . '/Models') : [];
            $files = array_diff($models, ['.', '..']);
            foreach ($files as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $class = $namespace . "\\Models\\" . $filename;

                if (class_exists($class)) {
                    $parents = class_parents($class);

                    if (in_array(EntityContext::class, $parents)) {
                        $entity = new $class(new EntityOptions());
                    }
                }
            }
        }

        Console::writeline("Build started...");

        if ($entity) {
            $migrator = new Migrator($entity->Database, $namespace . "\\Migrations", $workspace . "/Migrations");
            if ($target) {
                $migrator->migrate($target->Value);
            } else {
                $migrator->migrate();
            }
        } else {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("EntityContext not found.");
            Console::resetColor();
            exit;
        }

        Console::foregroundColor(ConsoleColor::Green);
        Console::writeline("Done.");
        Console::resetColor();
    }

    public function showHelp()
    {
        Console::writeline('Usage: devnet migrate [options]');
        Console::writeline();
        Console::writeline('Options:');
        Console::writeline('  --help     Displays help for this command.');
        Console::writeline('  --target   Displays help for this command.');
        Console::writeline();
        exit;
    }
}
