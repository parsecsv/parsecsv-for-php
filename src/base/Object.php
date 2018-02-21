<?php
namespace ParseCsv\base;


class Object {
    /**
     * Constructor.
     *
     * - Initializes the object with the given configuration `$config`.
     * - Call init().
     *
     * If this method is overridden in a child class, it is recommended that
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            self::configure($this, $config);
        }
        $this->init();
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
    }

    /**
     * Configures an object with the initial property values.
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @return object the object itself
     */
    public static function configure($object, $properties)
    {
        $objectClass = get_class($object);
        foreach ($properties as $name => $value) {
            if (property_exists($objectClass, $name)) {
                $object->$name = $value;
            } else {
                throw new \Exception('Unknown property: ' . $name);
            }
        }

        return $object;
    }
}
