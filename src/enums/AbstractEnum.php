<?php

namespace ParseCsv\enums;

use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

abstract class AbstractEnum
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Creates a new value of some type
     *
     * @param mixed $value
     *
     * @throws ReflectionException
     */
    public function __construct($value)
    {
        if (!self::isValid($value)) {
            throw new UnexpectedValueException("Value '$value' is not part of the enum " . static::class);
        }
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->value = $value;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public static function getConstants(): array
    {
        $class = static::class;
        $reflection = new ReflectionClass($class);

        return $reflection->getConstants();
    }

    /**
     * Check if enum value is valid
     *
     * @param mixed $value
     *
     * @return bool
     * @throws ReflectionException
     */
    public static function isValid($value): bool
    {
        return in_array($value, static::getConstants(), true);
    }
}
