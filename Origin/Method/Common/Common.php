<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2017
 */
/**
 * 应用环境公共文件引入函数
 * @param $guide
 * @return null;
 */
function Common($guide)
{
    $_receipt = null;
    if(strpos($guide,':')){
        $_url = str_replace(DS,':',str_replace('/', DS, "Application/")).$guide;
        $_obj = explode(':', $guide);
        $_receipt = Loading($_url,'.php');
    }
    return $_receipt;
}
/**
 * 文件及配置名规则函数
 * @param string $param
 * @return boolean
 */
function Rule($param)
{
    /**
     * @var string $_regular
     * @var boolean $_receipt
     */
    $_regular = '/^[^\_\W\s]+((\:|\\\|\_|\/)?[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match($_regular, $param)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $symbol
 * @return string
 * @contact 比较逻辑运算符双向转化方法
 */
function Symbol($symbol){
    /**
     * 符号替代值：
     * 大于：> - gt - greater than
     * 小于：< - lt - less than
     * 等于：= - eq - equal to
     * 大于等于：>= - ge - greater than and equal to
     * 小于等于：<= - le - less than and equal to
     * @var array $_symbol 符号关系数组
     * @var string $_receipt
     */
    $_receipt = '=';
    $_symbol = array('gt' => '>', 'lt' => '<', 'et'=> '=', 'eq' => '==','neq' => '!=', 'ge' => '>=', 'le' => '<=','heq' => '===', 'nheq' => '!==');
    if(array_key_exists(trim(strtolower($symbol)), $_symbol))
        $_receipt = $_symbol[trim(strtolower($symbol))];
    return $_receipt;
}
# 设置异常捕捉回调函数
register_shutdown_function("danger");
/**
 * @access public
 * @return array
 * @context 危险异常捕捉函数
 */
function danger()
{
    $_error = error_get_last();
    define("E_FATAL",  E_ERROR | E_USER_ERROR |  E_CORE_ERROR |
        E_COMPILE_ERROR | E_RECOVERABLE_ERROR| E_PARSE );
    if($_error && ($_error["type"]===($_error["type"] & E_FATAL))) {
        if(DEBUG){
            $_debug = new  \Origin\Kernel\Parameter\Output();
            $_debug->base($_error);
        }
    }
    return null;
}