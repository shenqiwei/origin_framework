<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/19
 * Time: 15:26
 */

namespace Origin\Kernel\Transaction\Example;

use Origin\Kernel\Data\Mysql as Layer;

class Query extends Layer
{
    /**
     * @var string $_Field 查询元素
     * 用于在select查询中精确查寻数据, 支持数组格式，同时支持as关键字
     */
    public $_Field = '*';
    /**
     * @var mixed $_Where
     * sql语句条件变量，分别为两种数据类型，当为字符串时，直接引用，当为数组时，转化执行
     */
    public $_Where = null;
    /**
     * @var mixed $_Group
     * 分组变量，与where功能支持相似
     */
    public $_Group = null;
    /**
     * @var string $_Order
     * 排序,与where功能支持相似
     */
    public $_Order = null;
    /**
     * @var mixed $_Limit
     * 查询界限值，int或者带两组数字的字符串
     */
    public $_Limit = null;

    function __construct($source)
    {
        parent::__construct($source);
    }
}