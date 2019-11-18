<?php
/**
 * IoC 框架路由注册文件，以数组为信息表述方式
 * 路由结构由：route（路由地址），mapping（映射地址），param（参数信息组成）
 * param 中参数可以为字符串型或数组类型 ,当为数组类型时，结构包含 name（名称），type（类型），default（默认值）
 * name（名称）: 字符串类型，不能为空，不能为纯数字
 * type（类型）: integer、int、string、boolean、float、double、 默认类型为string
 * default（默认值）: 可以为空，可以为 null，可以为0
 * 例：
 * array(
 *     'route' => 'new/index',
 *     'mapping' => 'Home/new/index',
 *     'param' => array(
 *         array('name' => 'id', 'type' => 'integer', 'default' => '0'),
 *     ),
 * )
 */
return array();