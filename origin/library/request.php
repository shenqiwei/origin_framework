<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2017
 */
/**
 * 请求器函数，通过调用内核控制器，实现请求结构功能，现有版本不支持put，delete请求支持
 * @access public
 * @param string $key 请求对象键
 * @param mixed $default 请求器返回默认值
 * @param boolean $delete 执行删除操作
 * @param string $type 请求类型
 * @return mixed
 */
function request($key, $default=null, $delete=false,$type="request")
{
    # 创建返回值变量
    $_receipt = null;
    # 创建请求范围变量
    $_regular = '/^[\w]+(\-[\w]+)*$/';
    # 判断请求参数是否合规
    if($key and is_true($_regular, trim(strtolower($key)))){
        # 判断默认值信息状态,并根据特例进行转换
        if($default){
            if(is_int($default)){
                $default = intval($default);
            }elseif(is_float($default) or is_double($default)){
                $default = doubleval($default);
            }elseif(is_bool($default)){
                $default = boolval($default);
            }elseif(is_numeric($default)){
                $default = strval($default);
            }
        }
        # 声明请求控制器对象
        $_obj= new Origin\Package\Request($key,$default,$type);
        if(is_bool($delete) and $delete === true){
            # 执行删除
            $_obj->delete();
        }else{
            # 执行请求验证，并接受返回值
            $_receipt =$_obj->main();
        }
    }
    return $_receipt;
}
/**
 * 请求器(GET)函数
 * @access public
 * @param string $key 请求对象键
 * @param mixed $default 请求器返回默认值
 * @param boolean $delete 执行删除操作
 * @return mixed
 */
function get($key, $default=null, $delete=false)
{
    return request($key, $default, $delete,"get");
}
/**
 * 请求器(POST)函数
 * @access public
 * @param string $key 请求对象键
 * @param mixed $default 请求器返回默认值
 * @param boolean $delete 执行删除操作
 * @return mixed
 */
function post($key, $default=null, $delete=false)
{
    return request($key, $default, $delete,"post");
}