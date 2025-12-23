<?php

namespace lff\DouyinPhp\Request;

class Request
{
    public function toArray()
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(); // 获取所有属性
        $arr = [];

        foreach ($properties as $prop) {
            $prop->setAccessible(true); // 允许访问私有/受保护属性
            $key = $prop->getName(); // 获取属性原名（无特殊字符）
            $arr[$key] = $prop->getValue($this); // 获取属性值
        }

        return $arr;
    }
}


