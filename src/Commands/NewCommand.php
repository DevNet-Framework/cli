<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Cli\Commands;

use Artister\System\Command\ICommand;
use Artister\System\Event\EventArgs;
use Artister\System\ConsoleColor;
use Artister\System\Console;

class NewCommand implements ICommand
{
    public function execute(object $sender, EventArgs $event) : void
    {
        $arguments  = $event->getAttribute('arguments');
        $template   = $arguments->getParameter('template');
        $help       = $arguments->getOption('--help');
        
        if ($help)
        {
            $this->showHelp();
        }
        
        if (!$template) {
            Console::foreGroundColor(ConsoleColor::Red);
            Console::writeline("Template not found");
            Console::writeline();
            Console::resetColor();
            exit;
        }

        $args = $arguments->Values;
        array_shift($args);

        $templateName = $template->Value ?? '';
        $templateName = strtolower($templateName);
        $templateName = ucfirst($templateName);

        $mainClass = "\\Artister\\Templates\\".$templateName."\\Program";
        if (class_exists($mainClass))
        {
            $mainClass::main($args);
        }
    }

    public function showHelp()
    {
        Console::writeline("Usage: devnet new [template] [arguments] [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help     Displays help for this command.");
        Console::writeline("  --project  Location to place the generated project.");
        Console::writeline();
        Console::writeline("templates:");
        Console::writeline("  console     Console Applicatinon");
        Console::writeline("  web         Web Applicatinon");
        Console::writeline("  controller  Controller Class");
        Console::writeline("  entity      Entity Class");
        Console::writeline();
        exit;
    }
}