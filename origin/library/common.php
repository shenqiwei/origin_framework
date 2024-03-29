<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 */
# 初始公共函数包
include("config.php");
include("log.php"); # 引用日志函数包
include("initialize.php");
include("exception.php");

/**
 * 文件地址链接符转化
 * @access public
 * @param string $uri 文件路径
 * @return string 返回转化后路径内容
*/
function replace(string $uri): string
{
    return str_replace(RE_DS,DS,$uri);
}

/**
 * 改变数组内容值全大写或全小写
 * @access public
 * @param array $array $对象数组
 * @param int $case 改变类型
 * @return array 返回执行后数组
 */
function array_change_value_case(array $array, int $case=CASE_LOWER): array
{
    foreach($array as $key => $value){
        if(!is_array($value)){
            if($case === CASE_UPPER)
                $array[$key] = strtoupper($value);
            else
                $array[$key] = strtolower($value);
        }
    }
    return $array;
}

/**
 * 自定义验证方法
 * @access public
 * @param string $regular 正则表达式
 * @param string $param 对象参数
 * @return bool
 */
function is_true(string $regular, string $param): bool
{
    $validate = new Origin\Package\Validate();
    return $validate->_type($param,$regular);
}

/**
 * 请求器函数，通过调用内核控制器，实现请求结构功能，现有版本不支持put，delete请求支持
 * @access public
 * @param string $key 请求对象键
 * @param mixed $default 请求器返回默认值
 * @param boolean $delete 执行删除操作
 * @param string $type 请求类型
 * @return array|string|null
 */
function request(string $key, $default=null, bool $delete=false, string $type="request")
{
    # 创建返回值变量
    $receipt = null;
    # 创建请求范围变量
    $regular = '/^[\w]+(\-[\w]+)*$/';
    # 判断请求参数是否合规
    if($key and is_true($regular, trim(strtolower($key)))){
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
        $obj= new Origin\Package\Request($key,$default,$type);
        if(is_bool($delete) and $delete === true){
            # 执行删除
            $obj->delete();
        }else{
            # 执行请求验证，并接受返回值
            $receipt =$obj->main();
        }
    }
    return $receipt;
}

/**
 * 请求器(GET)函数
 * @access public
 * @param string $key 请求对象键
 * @param mixed $default 请求器返回默认值
 * @param boolean $delete 执行删除操作
 * @return array|string|null
 */
function get(string $key, $default=null, bool $delete=false)
{
    return request($key, $default, $delete,"get");
}

/**
 * 请求器(POST)函数
 * @access public
 * @param string $key 请求对象键
 * @param mixed $default 请求器返回默认值
 * @param boolean $delete 执行删除操作
 * @return array|string|null
 */
function post(string $key, $default=null, bool $delete=false)
{
    return request($key, $default, $delete,"post");
}

/**
 * 会话基础操作函数
 * @access public
 * @param string $key 会话键名
 * @param mixed $value 会话值
 * @return mixed
*/
function session(string $key, $value=null)
{
    $session = new Origin\Package\Session();
    if(is_null($value)){
        return $session->get($key);
    }else{
        $session->set($key,$value);
        return null;
    }
}