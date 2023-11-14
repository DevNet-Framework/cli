<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\Cli\Templating\CodeGeneratorProvider;
use DevNet\Cli\Templating\CodeGeneratorRegistry;
use DevNet\Cli\Templating\CodeModel;
use DevNet\Cli\Templating\ICodeGenerator;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\Text\StringBuilder;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;
use DevNet\System\IO\FileAccess;
use DevNet\System\IO\FileException;
use DevNet\System\IO\FileMode;
use DevNet\System\IO\FileStream;
use DevNet\System\Runtime\ClassLoader;
use DOMDocument;
use ReflectionClass;

class AddCommand extends CommandLine implements ICommandHandler, ICodeGenerator
{
    private CodeGeneratorRegistry $registry;

    public function __construct()
    {
        parent::__construct('add', 'Add template code to the project.');

        $this->addArgument('template', 'The template code want to generate.');
        $this->addOption('--output', 'Location to place the generated code output.', '-o');
        $this->addOption('--name', 'Name of the generated code.', '-n');

        $this->registry = CodeGeneratorRegistry::getSingleton();
        $this->registry->set('class', new CodeGeneratorProvider('class', 'Generate an empty class file.', $this));

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

        $name = $args->get('--name');
        if (!$name) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine('The option --name is required!');
            Console::resetColor();
            return;
        }

        if (!$name->Value) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine('The option --name is missing an argument!');
            Console::resetColor();
            return;
        }

        $parameters[$name->Name] = $name->Value;
        $output = $args->get('--output');
        if ($output) {
            if (!$output->Value) {
                Console::$ForegroundColor = ConsoleColor::Red;
                Console::writeLine('The option --output is missing an argument!');
                Console::resetColor();
                return;
            }
            $parameters[$output->Name] = $output->Value;
        }

        $prefix      = 'Application';
        $sourceRoot  = getcwd();
        $projectPath = $sourceRoot . '/devnet.proj';

        // Gets root namespace from the project file and the source route related to the entrypoint location.
        if (is_file($projectPath)) {
            $dom = new DOMDocument();
            $result = $dom->load($projectPath);
            if ($result) {
                $rootNamespace = $dom->getElementsByTagName('RootNamespace')->item(0);
                $prefix = $rootNamespace ? $rootNamespace->textContent : 'Application';
                $startupObject = $dom->getElementsByTagName('StartupObject')->item(0);
                $mainClass = $startupObject ? $startupObject->textContent : 'Application\\Program';

                $loader = new ClassLoader($sourceRoot);
                $loader->map('/');
                $loader->register();

                if (class_exists($mainClass)) {
                    $mainClass = new ReflectionClass($mainClass);
                    $sourceRoot = dirname($mainClass->getFileName());
                }
            }
        }

        $parameters['--prefix'] = $prefix;

        $templateName = $template->Value;
        $templateName = strtolower($templateName);
        $provider     = $this->registry->get($templateName);
        $generator    = $provider->getGenerator();
        $models       = $generator->generate($parameters);
        $result       = 0;

        foreach ($models as $model) {
            try {
                $result = $this->create($model, $sourceRoot);
            } catch (FileException $exception) {
                Console::$ForegroundColor = ConsoleColor::Red;
                Console::writeLine($exception->getMessage());
                Console::resetColor();
                return;
            }
        }

        if (!$result) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("Something went wrong! failed to create {$template}.");
            Console::resetColor();
            return;
        }

        Console::$ForegroundColor = ConsoleColor::Green;
        Console::writeLine("The template '{$templateName}' was created successfully.");
        Console::resetColor();
    }

    public function generate(array $parameters): array
    {
        $name      = $parameters['--name'] ?? 'MyClass';
        $output    = $parameters['--output'] ?? '';
        $namespace = $parameters['--prefix'] ?? 'Application';
        $namespace = $namespace . '\\' . str_replace('/', '\\', $output);
        $namespace = trim($namespace, '\\');

        $content = new StringBuilder();
        $content->appendLine('<?php');
        $content->appendLine();
        $content->appendLine("namespace {$namespace};");
        $content->appendLine();
        $content->appendLine("class {$name}");
        $content->appendLine('{');
        $content->appendLine('}');

        return [new CodeModel($name . '.php', $content, $output)];
    }

    public function create(CodeModel $model, string $sourceRoot): int
    {
        $destination = implode('/', [$sourceRoot, $model->getRelativePath()]);
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $file = new FileStream($destination . '/' . $model->getFileName(), FileMode::Create, FileAccess::Write);
        $size = $file->write($model->getContent());
        $file->close();

        return $size;
    }
}
