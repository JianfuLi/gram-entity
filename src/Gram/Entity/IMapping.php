<?php
namespace Gram\Entity;

/**
 * Interface IMapping
 * @package Gram\Entity
 */
interface IMapping
{
    /**
     * 定义属性名称
     *
     * @param string $name
     *
     * @return IMapping
     */
    function map($name);

    /**
     * @param string $name
     *
     * @return IMapping
     */
    function id($name);

    /**
     * @return \Gram\Entity\Mapping\Metadata
     */
    function metadata();
} 