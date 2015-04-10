<?php
namespace Gram\Entity\Mapping;

use Gram\Entity\IEntity;
use Gram\Entity\Types;

/**
 * Class Metadata
 * @package Gram\DataMapper\Mapping
 */
class Metadata
{
    /**
     * @var array<Property>
     */
    public $properties;

    /**
     * @var Property
     */
    public $primaryKey;

    /**
     * @var string
     */
    public $className;

    /**
     * @param $className
     */
    function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param $name
     *
     * @return Property
     */
    function getProperty($name)
    {
        if (!isset($this->properties[$name])) {
            $this->properties[$name] = new Property($name);
        }
        return $this->properties[$name];
    }
}