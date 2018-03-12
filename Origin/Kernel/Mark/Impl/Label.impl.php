<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Mark.Impl.Label *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/02/03 16:04
 * update Time: 2017/02/03 16:04
 * chinese Context: IoC 标签解析器接口
 */
namespace Origin\Kernel\Mark\Impl;

interface Label
{
    /**
     * 默认函数，用于对模板中标签进行转化和结构重组
     * @access public
     * @return string
     */
    function execute();
    /**
     * 变量标签解释方法
     * @access protected
     * @param string $obj
     * @param string $variable
     * @param mixed $param
     * @param string $mapping
     * @return string
     */
    function variable($obj, $variable=null, $param=null, $mapping=null);
    /**
     * 逻辑判断标签解释方法
     * @access protected
     * @param string $obj
     * @return string
     */
    function judge($obj);
    /**
     * 逻辑批处理方法
     * @access protected
     * @param string $symbol
     * @param string $var
     * @param string $param
     * @return boolean;
     */
    function bool($symbol, $var, $param);
    /**
     * for循环标签解释方法
     * @access protected
     * @param string $obj
     * @return string
     */
    function traversal($obj);
    /**
     * foreach标签结构解释方法
     * @access public
     * @param string $obj
     * @return string
     */
    function loop($obj);
    /**
     * 函数动态调用解释方法
     * @access protected
     * @param string $function
     * @param string $param
     * @return string
     */
    function assign($function, $param);
}