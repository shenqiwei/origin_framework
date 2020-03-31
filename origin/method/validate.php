<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2017
 * @context:
 * 基础验证模块一共14
 * telephone：固定电话
 * mobile：移动电话 仅支持国内所有运营商号段
 * ip: ip地址 支持ipv4,ipv6
 * host：域名地址 支持多级域名及带参域名
 * chinese：中文
 * english：英文
 * numeric：纯数字
 * name：名字，可以同时支持中文英文混写，也可以用来支持名字中出现单字母的名字
 * nickname：昵称 支持中文英文数字及部分特殊符号,在一些特定条件下可以重构部分验证结构，提高验证精度
 * username：用户名 仅支持英文数字及部分特殊符号,在一些特定条件下可以重构部分验证结构，提高验证精度
 * weak：弱密码 支持纯英文数字或作为链接结构的部分特殊符号
 * strong：健壮密码 必须同时存在大写，小写，数字或部分特殊符号
 * safety：强密码 必须同时存在大写，小写，数据及部分特殊符号
 * 函数包将更加精确的使用验证结构包中的功能，比如在验证固定电话的模块中，派生出支持400,800的增强模块
*/
/**
 * 验证中文姓名方法，支持中文英文混写，也可以用来支持名字中出现单字母的名字
 * @access public
 * @param string $name
 * @return mixed
*/
function cn_name($name)
{
    $_validate = new Origin\Kernel\Validate($name);
    return $_validate->_type('/^[^\s\w\!\@\#\%\^\&\*\(\)\-\+\=\/\'\\"\$\:\;\,\.\<\>\`\~]+$/');
}
/**
 * 验证英文姓名方法
 * @access public
 * @param string $name
 * @return mixed
 */
function en_name($name)
{
    $_validate = new Origin\Kernel\Validate($name);
    return $_validate->_type('/^([A-Za-z]+[\.\s]?)+$/');
}
/**
 * 验证固定电话方法，支持加区号电话号码
 * @access public
 * @param string $number
 * @return mixed
 */
function is_tel($number)
{
    $_validate = new Origin\Kernel\Validate($number);
    return $_validate->_type('/^([0]{1}\d{2,3})?([^0]\d){1}\d{2,10}$/');
}
/**
 * 验证400和800固定电话方法
 * @access public
 * @param string $number
 * @return mixed
*/
function is_tel2($number)
{
    $_validate = new Origin\Kernel\Validate($number);
    return $_validate->_type('/^(800|400){1}[\-\s]?\d{4,6}[\-\s]?\d{4}$/');
}

/**
 * 验证移动电话号码
 * @access public
 * @param string $number
 * @return mixed
*/
function is_mobile($number)
{
    $_validate = new Origin\Kernel\Validate($number);
    return $_validate->_type('/^[1][3|4|5|7|8]{1}\d{9}$/');
}
/**
 * 邮箱验证方法
 * @access public
 * @param string $email
 * @return mixed
*/
function is_email($email)
{
    $_validate = new Origin\Kernel\Validate($email);
    return $_validate->_type('/^([^\_\W]+[\.\-]*)+\@(([^\_\W]+[\.\-]*)+\.)+[^\_\W]{2,8}$/');
}
/**
 * ipv4地址验证方法
 * @access public
 * @param string $ip
 * @return mixed
 */
function is_ipv4($ip)
{
    $_validate = new Origin\Kernel\Validate($ip);
    return $_validate->_ipv4();
}
/**
 * ipv6地址验证方法
 * @access public
 * @param string $ip
 * @return mixed
 */
function is_ipv6($ip)
{
    $_validate = new Origin\Kernel\Validate($ip);
    return $_validate->_ipv6();
}
/**
 * host地址验证方法
 * @access public
 * @param string $host
 * @return mixed
 */
function is_host($host)
{
    $_validate = new Origin\Kernel\Validate($host);
    return $_validate->_type('/^((http|https):\/\/)?(www.)?([\w\-]+\.)+[a-zA-z]+(\/[\w\-\.][a-zA-Z]+)*(\?([^\W\_][\w])+[\w\*\&\@\%\-=])?$/');
}
/**
 * 中文验证方法
 * @access public
 * @param string $cn
 * @return mixed
*/
function is_cn($cn)
{
    $_validate = new Origin\Kernel\Validate($cn);
    return $_validate->_type('/^[\x{4e00}-\x{9fa5}]+$/u'); # 中文检测需要在结尾夹u
}
/**
 * 英文验证方法
 * @access public
 * @param string $en
 * @return mixed
*/
function is_en($en)
{
    $_validate = new Origin\Kernel\Validate($en);
    return $_validate->_type('/^[^\_\d\W]+$/');
}
/**
 * 验证用户名方法
 * @access public
 * @param string $username
 * @return mixed
*/
function is_username($username)
{
    $_validate = new Origin\Kernel\Validate($username);
    return $_validate->_type('/^[\w\.\-\@\+\$\#\*\~\%\^\&]+$/');
}
/**
 * 弱密码验证方法
 * @access public
 * @param string $password
 * @return mixed
*/
function weak_password($password)
{
    $_validate = new Origin\Kernel\Validate($password);
    return $_validate->_type('/^([^\_\W]+([\_\.\-\@\+\$\#\*\~\%\^\&]*))+$/');
}
/**
 * 强密码验证方法
 * @access public
 * @param string $password
 * @return mixed
*/
function strong_password($password)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Validate($password);
    return $_validate->_type('/^([A-Z]+[a-z]+[0-9]+[\.\_\-\@\+\$\#\*\~\%\^\&]*)+$/');
}
/**
 * 安全密码验证方法
 * @access public
 * @param string $password
 * @return mixed
*/
function safe_password($password)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Validate($password);
    return $_validate->_type('/^([A-Z]+[a-z]+[0-9]+[\.\_\-\@\+\$\#\*\~\%\^\&]+)+$/');
}
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
    $_validate = new Origin\Kernel\Validate($param);
    if($_receipt = $_validate->_empty()){
        if($_receipt = $_validate->_size($min,$max))
            $_receipt = $_validate->_type($regular);
    }
    return $_receipt;
}
