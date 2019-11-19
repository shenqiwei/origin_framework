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
 * @param string $type
 * @return mixed
*/
function is_name($name, $type='cn')
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     * @var object $_type
    */
    $_type = 'cn_name';
    if($type == 'en') $_type = $type.'_name';
    $_validate = new Origin\Kernel\Parameter\Validate($name, $_type, 1);
    return $_validate->main();
}
/**
 * 验证固定电话方法，支持加区号电话号码，同时对400和800进行支持
 * @access public
 * @param string $number
 * @param string $type
 * @return mixed
*/
function is_tel($number, $type=null)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     * @var string $_type
     * @var string $_regular
     */
    $_type  = 'telephone';
    if($type == 'telecom') $_type = 'redefine';
    $_validate = new \Origin\Kernel\Parameter\Validate($number, $_type);
    if($type == 'telecom'){
        $_regular = '/^(800|400){1}[\-\s]?\d{4,6}[\-\s]?\d{4}$/';
        $_validate->regular($_regular);
    }
    return $_validate->main();
}

/**
 * 验证移动电话号码
 * @access public
 * @param string $number
 * @return mixed
*/
function is_mobile($number)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($number, 'mobile');
    return $_validate->main();
}
/**
 * 邮箱验证方法
 * @access public
 * @param string $email
 * @return mixed
*/
function is_email($email)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($email, 'email');
    return $_validate->main();
}
/**
 * ip地址验证方法
 * @access public
 * @param string $ip
 * @param string $type
 * @return mixed
 */
function is_ip($ip, $type = 'ipv4')
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     * @var string $_regular
     */
    $_type = 'ipv4';
    if($type == 'ipv6') $_type = 'ipv6';
    $_validate = new Origin\Kernel\Parameter\Validate($ip, $_type);
    return $_validate->main();
}
/**
 * host地址验证方法
 * @access public
 * @param string $host
 * @return mixed
 */
function is_host($host)
{
    $_validate = new Origin\Kernel\Parameter\Validate($host, 'host');
    return $_validate->main();
}
/**
 * 中文验证方法
 * @access public
 * @param string $cn
 * @return mixed
*/
function is_cn($cn)
{
    /**
      * 调用验证结构包，并声明验证对象
      * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($cn, 'chinese');
    return $_validate->main();
}
/**
 * 英文验证方法
 * @access public
 * @param string $en
 * @return mixed
*/
function is_en($en)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($en, 'english');
    return $_validate->main();
}
/**
 * 验证用户名方法
 * @access public
 * @param string $username
 * @param int $min
 * @param int $max
 * @return mixed
*/
function is_username($username, $min=0, $max=0)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($username, 'username', $min, $max);
    return $_validate->main();
}
/**
 * 弱密码验证方法
 * @access public
 * @param string $password
 * @param int $min
 * @param int $max
 * @return mixed
*/
function weak_password($password, $min=0, $max=0)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($password, 'weak', $min, $max);
    return $_validate->main();
}
/**
 * 强密码验证方法
 * @access public
 * @param string $password
 * @param int $min
 * @param int $max
 * @return mixed
*/
function strong_password($password, $min=0, $max=0)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($password, 'strong', $min, $max);
    return $_validate->main();
}
/**
 * 安全密码验证方法
 * @access public
 * @param string $password
 * @param int $min
 * @param int $max
 * @return mixed
*/
function safe_password($password, $min=0, $max=0)
{
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($password, 'safety', $min, $max);
    return $_validate->main();
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
    /**
     * 调用验证结构包，并声明验证对象
     * @var object $_validate
     */
    $_validate = new Origin\Kernel\Parameter\Validate($param, 'redefine', $min, $max);
    $_validate->regular($regular);
    return $_validate->main();
}
