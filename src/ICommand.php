<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli;

use DevNet\System\Event\EventArgs;

interface ICommand
{
    public function execute(object $sender,  EventArgs $event): void;
}
