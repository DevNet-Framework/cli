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

        $templateName = $template->Value;
        $templateName = strtolower($templateName);
        $provider     = $this->registry->get($templateName);
        $generator    = $provider->getGenerator();

        $models = $generator->generate($parameters);

        foreach ($models as $model) {
            $result = $this->create($model);
            if (!$result) {
                Console::$ForegroundColor = ConsoleColor::Red;
                Console::writeLine("Somthing whent wrong! faild to create {$template}.");
                Console::resetColor();
                return;
            }
        }

        Console::$ForegroundColor = ConsoleColor::Green;
        Console::writeLine("The template '{$templateName}' was created successfully.");
        Console::resetColor();
    }

    public function generate(array $parameters): array
    {
        $name      = $parameters['--name'] ?? 'MyClass';
        $output    = $parameters['--output'] ?? '';
        $output    = str_replace('/', '\\', $output);
        $namespace = 'Application\\' . $output;
        $namespace = trim($namespace, '\\');
        $namespace = ucwords($namespace, '\\');

        $content = new StringBuilder();
        $content->appendLine('<?php');
        $content->appendLine();
        $content->appendLine("namespace {$namespace};");
        $content->appendLine();
        $content->appendLine("class {$name}");
        $content->appendLine('{');
        $content->appendLine('}');

        return [new CodeModel($name . '.php', $content)];
    }

    public function create(CodeModel $model): bool
    {
        $destination = implode('/', [getcwd(), $model->getRelativePath()]);

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $file = fopen($destination . '/' . $model->getFileName(), 'w');
        $size   = fwrite($file, $model->getContent());
        $status = fclose($file);

        if (!$size || !$status) {
            return false;
        }

        return true;
    }
}
