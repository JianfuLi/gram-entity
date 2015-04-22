<?php
namespace Gram\Entity;

/**
 * Class EntityBase
 *
 * @package Gram\DataMapper
 */
abstract class EntityBase implements IEntity
{
    use EntityMapper;

    /**
     * @var array
     */
    static private $rcContainer = array();

    /**
     * @return \ReflectionClass
     */
    protected function getReflector()
    {
        if (!isset(self::$rcContainer[__CLASS__])) {
            self::$rcContainer[__CLASS__] = new \ReflectionClass($this);
        }
        return self::$rcContainer[__CLASS__];
    }

    /**
     * @return EntityIterator
     */
    public function getIterator()
    {
        $md = static::metadata();
        $properties = array();
        foreach ($md->properties as $name => $md) {
            $properties[$name] = $this->{$name};
        }

        $reflector = $this->getReflector();
        $ps = $reflector->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($ps as $p) {
            if ($p->isStatic()) {
                continue;
            }
            $properties[$p->getName()] = $this->{$p->getName()};
        }
        return new EntityIterator($properties);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * @param mixed $offset
     *
     * @return null
     */
    function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * @param mixed $offset
     */
    function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        //return self::metadata()->disassemble($this);
        $obj = array();
        foreach ($this as $k => $v) {
            $obj[$k] = $v;
            //$obj[$k] = $v instanceof \DateTime ? $v->format(self::dateTimeFormat()) : $v;
        }
        return $obj;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return json_encode($this->jsonSerialize());
    }
}
