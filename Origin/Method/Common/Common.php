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
 * 公共信息处理函数
 * @access public
 * @param $model
 * @param $message
 * @param $url
 * @param $time
 * @return null
 */
function message($model, $message='this is a message',$url='#',$time=5)
{

}
/**
 * 文件及导向结构规则函数
 * @param string $uri
 * @return boolean
 */
function fileUri($uri)
{
    /**
     * @var string $_regular
     * @var boolean $_receipt
     */
    $_regular = '/^[^\_\W\s]+((\_|\/)?[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match($_regular, $uri)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * 文件及导向结构规则函数
 * @param string $uri
 * @return boolean
 */
function formatGuide($uri)
{
    /**
     * @var string $_regular
     * @var boolean $_receipt
     */
    $_regular = '/^[^\_\W\s]+((\_|\:)?[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match($_regular, $uri)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @var string $_file_format
 * @return boolean
 * @contact 文件路径格式验证
 */
function formatFile($uri)
{
    /**
     * @var string $_file_format
     * @var boolean $_receipt
     */
    $_file_format = '/^([^\_\W\s]+[\:](\\\|\/))?[^\_\W\s]+((\_|\\\|\/)?[^\_\W\s]+)*(\.[^\_\W\s]+)+$/u';
    $_receipt = false;
    if(preg_match($_file_format, $uri)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $name
 * @return boolean
 * @contact 文件名格式验证
 */
function nameFile($name)
{
    /**
     * @var string $_name_format
     * @var boolean $_receipt
     */
    $_name_format = '/^[^\_\W\s]+(\_[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match($_name_format, $name)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $type
 * @return boolean
 * @contact 文件属性结构信息
*/
function formatType($type)
{
    /**
     * @access private
     * @var string $_type_format 文件属性名约束
     */
    $_type_format = '/^(class|func|function|impl|implements|interface|controller|method|common|cfg|config|action|data|file|graph|math|message|info|param|bean|beans|map|mapping|filter|model|view)$/u';
    $_receipt = false;
    if(preg_match($_type_format, $type)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $suffix 文件扩展名信息
 * @return boolean
 * @contact 文件宽展名约束
*/
function formatSuffix($suffix)
{
    /**
     * @access private
     * @var string $_auto_type 自定义文件属性约束
     */
    $_auto_suffix = '/^(php|phpx|php5|php7|xhtml|html|htm|log|ini|txt)$/u';
    $_receipt = false;
    if(preg_match($_auto_suffix, $suffix)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $mapping
 * @return string
 * @contact 文件结构映射控制
*/
function suffixMap($mapping)
{
    $_mapping = array(
        'controller' => 'class', 'function' => 'func', 'method' => 'func', 'common' => 'func',
        'config' => 'cfg', 'action' => 'act', 'message' => 'info', 'param' => 'bean', 'beans' => 'bean',
        'map' => 'mapping', 'implements' => 'impl', 'interface' => 'impl',
    );
    $_receipt = null;
    if(key_exists($mapping,$_mapping)){
        $_receipt = $_mapping[$mapping];
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $url
 * @return boolean
 * @contact 访问地址结构格式
*/
function urlGuide($url)
{
    $_url_format = '/^((http|https)://)?[^\_\W\s]+((\_|\/)[^\_\W\s]+)*(\.[^\_\W\s\d]+)?(\?[^\_\W\s]+(\_[^\_\W\s]+)*\=[^\&\s]+((\&)[^\_\W\s]+(\_[^\_\W\s]+)*\=[^\&\s]+)*)?$/u';
    $_receipt = false;
    if(preg_match($_url_format, $url)){
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