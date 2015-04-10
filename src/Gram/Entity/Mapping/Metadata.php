<?php
namespace Gram\Entity\Mapping;

use Gram\Entity\IEntity;

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

    /**
     * @param array $row
     */
    function assemble(array $row)
    {
        if (empty($row))
            return null;

        $entity = new $this->className;
        $ps = $this->properties;
        foreach ($ps as $p) {
            if (!isset($row[$p->column])) {
                continue;
            }
            if (is_null($p->setter)) {
                $entity->{$p->name} = $row[$p->column];
            } else {
                $entity->{$p->name} = call_user_func($p->setter, $row, $p->column);
            }
        }
        return $entity;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    function assembleAll(array $rows)
    {
        $items = array();
        $hasPrimaryKey = !is_null($this->primaryKey);
        foreach ($rows as $row) {
            $item = $this->assemble($row);
            if (is_null($item)) {
                continue;
            }
            if ($hasPrimaryKey) {
                $items[$item->{$this->primaryKey->name}] = $item;
            } else {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @param IEntity $entity
     *
     * @return array
     */
    function disassemble(IEntity $entity)
    {
        $arr = array();
        $ps = $this->properties;
        foreach ($ps as $p) {
            if (is_null($p->getter)) {
                $arr[$p->column] = $entity->{$p->name};
            } else {
                $arr[$p->column] = call_user_func($p->getter, $entity, $p->name);
            }
        }

        return $arr;
    }
} 