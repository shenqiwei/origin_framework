<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2017
*/
/**
 * 自定义验证方法
 * @access public
 * @param string $regular
 * @param string $param
 * @param int $min
 * @param int $max
 * @return mixed
*/
function is_true($regular, $param, $min=0, $max=0)
{
    $_validate = new Origin\Package\Validate($param);
    if($_receipt = $_validate->_empty()){
        if($_receipt = $_validate->_size($min,$max))
            $_receipt = $_validate->_type($regular);
    }
    return $_receipt;
}
