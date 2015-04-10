<?php
namespace Gram\Entity;

interface IMapper
{
    /**
     * 定义属性名称
     *
     * @param string $name
     *
     * @return IMapper
     */
    function map($name);

    /**
     * @param string $name
     *
     * @return IMapper
     */
    function id($name);

    /**
     * @return Metadata
     */
    function metadata();
} 