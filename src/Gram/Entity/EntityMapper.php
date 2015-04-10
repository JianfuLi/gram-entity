<?php
namespace Gram\Entity;

use Gram\Entity\Mapping\Mapping;
use Gram\Entity\Mapping\Metadata;
use Gram\Entity\Exception\TypeException;
use Gram\Entity\Exception\PropertyException;
use Gram\Entity\Exception\NotImplementedException;

/**
 * trait EntityMapper
 * @package Gram\Entity
 */
trait EntityMapper
{
    /**
     * @var array
     */
    protected static $container = array();

    /**
     * @var array
     */
    protected $ps = array();

    /**
     * @param string $name
     *
     * @return mixed
     * @throws PropertyException
     * @throws TypeException
     */
    function __get($name)
    {
        $md = self::metadata();
        if (!isset($md->properties[$name])) {
            throw new PropertyException('试图访问未定义的属性' . $name);
        }

        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        $m = $md->properties[$name];
        if (is_null($m->type)) {
            throw new TypeException('未定义属性' . $name . '的类型');
        }
        $value = isset($this->ps[$name])
            ? $this->ps[$name]
            : null;
        return Types::convert($value, $m->type);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws PropertyException
     * @throws TypeException
     */
    function __set($name, $value)
    {
        $md = self::metadata();
        if (!isset($md->properties[$name])) {
            throw new PropertyException('试图访问未定义的属性' . $name);
        }

        $m = $md->properties[$name];
        if (is_null($m->type)) {
            throw new TypeException('未定义属性' . $name . '的类型');
        }

        $castValue = Types::convert($value, $m->type);
        foreach ($m->validators as $v) {
            if ($v instanceof IValidatable) {
                $v->validate($castValue);
            }
        }

        $methodName = 'set' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            return $this->$methodName($castValue);
        } else {
            $this->ps[$name] = $castValue;
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    function __isset($name)
    {
        $md = self::metadata();
        return isset($md->properties[$name]);
    }

    /**
     * 克隆对象本身
     *
     * @return static
     */
    function __clone()
    {
        $new = new static;
        foreach ($this as $k => $v) {
            $new->{$k} = $v;
        }
        return $new;
    }

    /**
     * 初始化对象属性配置
     *
     * @param IMapping $map
     *
     * @throws NotImplementedException
     */
    static protected function __init(IMapping $map)
    {
        throw new NotImplementedException('未实现initMetadata方法');
    }

    /**
     * 获取对象元数据
     *
     * @return Metadata
     * @throws \Exception
     */
    static function metadata()
    {
        $className = get_called_class();
        if (!isset(self::$container[$className])) {
            self::$container[$className] = self::initMetadata($className);
        }
        return self::$container[$className];
    }

    /**
     * 初始化元数据
     *
     * @param $className
     *
     * @return IMapping
     * @throws NotImplementedException
     */
    private static function initMetadata($className)
    {
        $mapper = self::newMapping($className);
        self::__init($mapper);
        return $mapper;
    }

    /**
     * 创建新的映射
     *
     * @param $className
     *
     * @return IMapping
     */
    protected static function newMapping($className)
    {
        return new Mapping($className);
    }

    /**
     * 默认的时间格式
     *
     * @return string
     */
    static function dateTimeFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * 将数组按照映射的配置组装成对象
     *
     * @param array $row
     *
     * @return mixed
     */
    static function assemble(array $row)
    {
        if (empty($row))
            return null;

        $ps = self::metadata()->properties;
        $className = get_class();
        $entity = new $className;
        foreach ($ps as $p) {
            if (!isset($row[$p->column])) {
                continue;
            }
            if (is_null($p->setter)) {
                if ($p->type === Types::TYPE_DATETIME) {
                    $entity->{$p->name} = new \DateTime($row[$p->column]);
                } else {
                    $entity->{$p->name} = $row[$p->column];
                }
            } else {
                $entity->{$p->name} = call_user_func($p->setter, $row, $p->column);
            }
        }
        return $entity;
    }

    /**
     * 将批量的数组按照映射的配置组装成对象
     *
     * @param array $rows
     *
     * @return array
     */
    static function assembleAll(array $rows)
    {
        $items = array();
        $metadata = self::metadata();
        $hasPrimaryKey = !is_null($metadata->primaryKey);
        foreach ($rows as $row) {
            $item = self::assemble($row);
            if (is_null($item)) {
                continue;
            }
            if ($hasPrimaryKey) {
                $items[$item->{$metadata->primaryKey->name}] = $item;
            } else {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * 将对象按照映射的配置转换成数组
     *
     * @param $entity
     *
     * @return mixed
     */
    static function disassemble($entity)
    {
        $arr = array();
        $ps = self::metadata()->properties;
        foreach ($ps as $p) {
            if (is_null($p->getter)) {
                if ($p->type === Types::TYPE_DATETIME) {
                    $format = call_user_func(array(get_class(), 'dateTimeFormat'));
                    $arr[$p->column] = $entity->{$p->name}->format($format);
                } else {
                    $arr[$p->column] = $entity->{$p->name};
                }
            } else {
                $arr[$p->column] = call_user_func($p->getter, $entity, $p->name);
            }
        }

        return $arr;
    }
}