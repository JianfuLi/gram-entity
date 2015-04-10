<?php
namespace Gram\Entity\Mapping;

/**
 * Class Mapper
 * @package Gram\Entity\Mapping
 */
class Mapper implements IMapper
{
    /**
     * @var Metadata
     */
    protected $metadata = null;

    /**
     * @param string $className
     */
    function __construct($className)
    {
        $this->metadata = new Metadata($className);
    }

    /**
     * 定义属性名称
     *
     * @param string $name
     *
     * @return PropertyMapper
     */
    function map($name)
    {
        $property = $this->metadata->getProperty($name);
        return new PropertyMapper($property);
    }

    /**
     * @param string $name
     *
     * @return PropertyMapper
     */
    function id($name)
    {
        $this->metadata->primaryKey = $this->metadata->getProperty($name);
        return new PropertyMapper($this->metadata->primaryKey);
    }

    /**
     * @return Metadata
     */
    function metadata()
    {
        return $this->metadata;
    }
} 