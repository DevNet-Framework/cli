<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IEnumerable;
use DevNet\System\Type;

abstract class AbstractRegistry implements IEnumerable
{
    private Type $type;
    private array $classes = [];
    private array $objects = [];

    abstract public static function getSingleton(): static;

    public function __construct(string $type)
    {
        $this->type = new Type($type);
    }

    /**
     * @param object|string $service object or class name of the injected service
     */
    public function set(string $name, $service): void
    {
        if (is_string($service)) {
            $this->classes[$name] = $service;
        } else if (is_object($service)) {
            if ($this->type->isTypeOf($service)) {
                $this->objects[$name] = $service;
            }
        }
    }

    public function get(string $name): ?object
    {
        $object = $this->objects[$name] ?? null;
        if ($object) {
            return $object;
        }

        $class = $this->classes[$name] ?? '';
        if (class_exists($class)) {
            $object = new $class();
            if ($this->type->isTypeOf($object)) {
                $this->objects[$name] = $object;
                return $object;
            }
        }

        return null;
    }

    public function getIterator(): Enumerator
    {
        $names = array_keys($this->classes);
        foreach ($names as $name) {
            $this->get($name);
        }

        return new Enumerator($this->objects);
    }

    public static function register(string $name, string $class): void
    {
        static::getSingleton()->set($name, $class);
    }
}
