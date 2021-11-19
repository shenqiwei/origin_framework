<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context Origin框架路由配置文件
 */
return [
    /*
     * 路由配置信息为多为数组结构，数组第一维度键名为路由访问地址信息
     * mapping：路由映射地址指向 (string)，该数组键对应值为路由数组键名，可重复使用，在明确路由结构数组中禁用该键
     * method：路由访问方式 (string) 限制值: get|post
     * classes：应用类映射地址 (string)
     * functions：应用函数名称 (string)，默认函数 index
     * 例：
     * [
     *     "/" => ["mapping" => 'home'],
     *     "/index" => ["mapping" => 'home'],
     *     "/default" => ["mapping" => 'home'],
     *     "/main/index" => ["mapping" => 'home'],
     *     "home"=>[
     *          "method" => "get",
     *          "classes" => "Application/Home/Classes/Index",
     *          "functions"=>"index"
     *     ],
     *     "/main/s" => [
     *          "method" => "get",
     *          "classes" => "Application/Home/Classes/Index",
     *          "functions"=>"speed"
     *     ]
     * ],
     */
    [
        "/" => ["mapping" => 'home'],
        "/index" => ["mapping" => 'home'],
        "/default" => ["mapping" => 'home'],
        "/main/index" => ["mapping" => 'home'],
        "home"=>[
            "method" => "get",
            "class" => "Application/Home/Classes/Index",
            "functions"=>"index"
        ],
        "/main/s" => [
            "method" => "get",
            "class" => "Application/Home/Classes/Index",
            "functions"=>"welcome"
        ]
    ],
];