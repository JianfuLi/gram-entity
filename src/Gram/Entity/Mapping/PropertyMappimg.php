<?php
namespace Gram\Entity\Mapping;

use Gram\Entity\IValidatable;

/**
 * Class PropertyMapping
 *
 * @package Gram\DataMapper\Mapping
 */
class PropertyMapper
{
    /**
     * @var Property
     */
    protected $property;

    /**
     * @param Property $property
     */
    function __construct(Property &$property)
    {
        $this->property = $property;
    }


    /**
     * @param IValidatable $v
     *
     * @return PropertyMapper
     */
    function validator(IValidatable $v)
    {
        $this->property->validators[] = $v;
        return $this;
    }

    /**
     * 类型，必须使用完整的类名
     *
     * 如：Gram\DataMapper\EntityBase
     *
     * @param string $type
     *
     * @return PropertyMapper
     */
    function type($type)
    {
        $this->property->type = $type;
        return $this;
    }

    /**
     * @param bool $autoIncrement
     *
     * @return PropertyMapper
     */
    function autoIncrement($autoIncrement = true)
    {
        $this->property->autoIncrement = $autoIncrement;
        return $this;
    }

    /**
     * 是否必须
     * @return PropertyMapper
     */
    function required()
    {
        $this->property->required = true;
        return $this;
    }

    /**
     * 列名
     *
     * @param string $column
     *
     * @return PropertyMapper
     */
    function column($column)
    {
        $this->property->column = $column;
        return $this;
    }

    /**
     * @param callable $getter
     * @param callable $setter
     *
     * @return PropertyMapper
     */
    function converter(\Closure $getter, \Closure $setter)
    {
        $this->property->getter = $getter;
        $this->property->setter = $setter;
        return $this;
    }
} 