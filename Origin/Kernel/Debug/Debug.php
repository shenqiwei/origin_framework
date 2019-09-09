<?php
/**
 * @author 沈启威(ShenQiwei) <cheerup.shen@foxmail.com>
 * @createTime 2018-10-25
 * @deprecated Zeroes框架调试封装类
 * @copyright Copyright (c) 2018, ShenQiwei
 * @package Zeroes
 * @version 1.0
 */
namespace Zeroes\Package\Debug;
/*
 * 封装类
*/
class Debug
{
    /**
     * @access public
     * @param array $error_arr 异常信息数组
     * @return null
     * @context 底层异常显示模块
    */
    public function base($error_arr)
    {
        $_error_msg = $error_arr["message"];
        $_error_msg = explode("#",$_error_msg);
        include(ROOT_ADDRESS."/Template/Debug.html");
        return null;
    }
}