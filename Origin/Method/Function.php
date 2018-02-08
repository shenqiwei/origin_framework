<?php
/**
-*- coding: utf-8 -*-
-*- system OS: windows2008 -*-
-*- work Tools:Phpstorm -*-
-*- language Ver: php7.1 -*-
-*- agreement: PSR-1 to PSR-11 -*-
-*- filename: IoC.Origin.Function.function-*-
-*- version: 0.1-*-
-*- structure: common framework -*-
-*- designer: 沈启威 -*-
-*- developer: 沈启威 -*-
-*- partner: 沈启威 , 任彦明 -*-
-*- chinese Context:
-*- create Time: 2017/01/09 15:34
-*- update Time: 2017/01/09 15:34
-*- IoC 主方法函数包
 */
# 框架柱目录文件路径
if(!defined('RING')) define('RING', 'Origin'.SLASH);
# 公共配置常量
if(!defined('CLASS_SUFFIX')) define('CLASS_SUFFIX', Config('CLASS_SUFFIX'));
if(!defined('METHOD_SUFFIX')) define('METHOD_SUFFIX', Config('METHOD_SUFFIX'));
if(!defined('CONFIG_SUFFIX')) define('CONFIG_SUFFIX', Config('CONFIG_SUFFIX'));
# 判断程序是否锁定域名
if(Config('URL_HOST_ONLY') != 0){
    # 判断访问地址与注册域名是否相同
    if(Config('URL_HOST') != $_SERVER['HTTP_HOST']){
        # 不同抛出错误
        # 异常处理：访问域名与注册域名不符
        try{
            throw new \Exception('error: Access to the domain name does not accord with registered domain name');
        }catch(\Exception $e){
            echo($e->getMessage());
            exit(0);
        }
    }
}
# 创建基础常量
# 默认应用访问目录，默认为空，当进行web开发时，区分前后台时，填入并在Apply下建立同名文件夹
if(!defined('__APPLICATION__')) define('__APPLICATION__', Config('DEFAULT_APPLICATION'));
# 协议类型
if(!defined('__PROTOCOL__')) define('__PROTOCOL__', empty($_SERVER['HTTP_X_CLIENT_PROTO'])? 'http://' : 'https://');
# 地址信息
if(!defined('__HOST__')) define('__HOST__',__PROTOCOL__.$_SERVER['HTTP_HOST'].'/');
# 插件应用常量
if(!defined('__PLUGIN__')) define('__PLUGIN__', Config('ROOT_PLUGIN'));
# 资源应用常量
if(!defined('__RESOURCE__')) define('__RESOURCE__', Config('ROOT_RESOURCE'));
# 资源目录常量
define('__JSCRIPT__',__RESOURCE__.Config('ROOT_RESOURCE_JS'));
define('__MEDIA__',__RESOURCE__.Config('ROOT_RESOURCE_MEDIA'));
define('__STYLE__',__RESOURCE__.Config('ROOT_RESOURCE_STYLE'));
define('__TEMP__',__RESOURCE__.Config('ROOT_RESOURCE_TEMP'));

/**
 * 公共配置信息引导函数
 * @access public
 * @param string $guide 配置名称，不区分大小写
 * @return string
*/
function Config($guide)
{
    /**
     * @var array $_array
     * @var string $_receipt
     * @var mixed $_config
    */
    # 创建结果集变量
    $_receipt = null;
    # 创建配置结构变量
    $_config = null;
    # 创建配置寄存变量
    $_array = Import('Config:Config');
    # 判断引导参数是否有效
    if(Rule($guide)){
        # 判断参数中是否存在引导连接符，当存在引导连接符，则将参数转为数组并赋入配置变量中，反之则直接赋入配置变量中
        if(strpos($guide,':')){
            $_config = explode(':',$guide);
        }else{
            $_config = strval(trim(strtoupper($guide)));
        }
        # 配置变量是否为数组，跟返回状态执行不同的操作
        if(is_array($_config)){
            # 遍历引导信息
            for($i=0;$i<count($_config);$i++){
                # 判断数组元素信息是否为数组中的键名，如果是将对应元素值信息存入数组变量中，
                if(array_key_exists(strtoupper($_config[$i]), $_array)){
                    $_array = $_array[strtoupper($_config[$i])];
                    # 判断元素值是否为数组，如果是继续进行查找和验证，反之赋入返回变量中
                    if(is_array($_array)){
                        continue;
                    }else{
                        $_receipt = $_array;
                        break;
                    }
                }
            }
        }else{
            # 判断当前配置名称是否存在于配置中，如果存在赋入返回变量中
            if(array_key_exists(strtoupper($_config), $_array)){
                $_receipt = $_array[strtoupper($_config)];
            }
        }
    }
    return $_receipt;
}
/**
 * 文件检索及加载函数,处理预设结构类型
 * @access public
 * @param string $guide 文件路径，使用 :（冒号）作为连接符
 * @param string $type 文件类型，用于区分不同作用文件，基础类型class（类），func（函数），cfg（配置）
 * @param string $suffix 文件扩展名，文件扩展与文件类型名组成，完整的文件扩展名。例如：.class.php / .cfg.php
 * @param string $throws 是否抛出异常信息
 * @return null
 */
function Hook($guide, $type=null, $suffix=null, $throws='enable')
{
    /**
     * @var mixed $_hook 指引结构数组
     * @var mixed $_type 预设文件类型正则表达式
     * @var mixed $_array 文件类型描述结构数组
     * @var string $_regular 文件扩展名结构正则表达式
     * @var string $_folder 文件夹物理路径
     * @var string $_danger
     * @var string $_file 文件名变量
     * @var string $_suffix 扩展名
     * @var int $i
    */
    $_receipt = null;
    # 判断指引信息是否为空
    if($guide){
        # 判断连接符号是否存在
        if(Rule($guide) and strpos($guide,':')){
            # 将指引信息转为数组结构
            $_hook = explode(':',$guide);
            # 创建根路径信息
            $_folder = ROOT;
            # 创建文件域类型作用范围
            $_type = '/^(class|func|function|impl|implements|interface|controller|method|common|cfg|config|action|data|file|
            graph|math|message|info|param|bean|beans|map|mapping|filter|model||auto)$/';
            # 双结构解释数组
            $_array = array(
                'controller' => 'class', 'function' => 'func', 'method' => 'func', 'common' => 'func',
                'config' => 'cfg', 'action' => 'act', 'message' => 'info', 'param' => 'bean', 'beans' => 'bean',
                'map' => 'mapping', 'implements' => 'impl', 'interface' => 'impl',
            );
            # 自定义文件域名公式
            $_regular = '/^[\.][^\_\W]+([\.][^\_\W]+)*$/';
            # 非法结构
            $_danger = '/([\W\_])+/';
            # 替换结构
            $_replace = '.';
            # 创建文件名空变量
            $_file = null;
            # 限定文件扩展名
            $_suffix = '.php';
            # 循环指引路径数组
            for($i=0;$i<count($_hook);$i++){
                # 判断是否是最后一个组数元素，当遍历到最后一个元素时，跳过验证结构
                if($i == count($_hook)-1){
                    $_file = SLASH.$_hook[$i];
                    continue;
                }else{
                    # 组装路径信息，随遍历深度进行路径拼接
                    if($i==0){
                        $_folder = $_folder.$_hook[$i];
                    }else{
                        $_folder = $_folder.SLASH.$_hook[$i];
                    }
                    # 判断每次遍历组装后的文件夹路径是否存在，当该路径存在是跳过，反之抛出异常信息
                    if(is_dir($_folder)){
                        continue;
                    }else{
                        # 异常提示：文件夹地址不存在
                        if($throws != 'disabled') {
                            try {
                                throw new Exception('Origin Method Error[1001]: The folder address ' . $_folder . ' does not exist');
                            } catch (Exception $e) {
                                echo($e->getMessage());
                                exit();
                            }
                        }
                        break;
                    }
                }
            }
            # 判断文件类型是否符合要求
            if(preg_match($_type, $type)){
                # 判断例外规则
                if($type == 'auto'){
                    # 调用用户自定义文件类型，当文件不为空时，使用用户自定义类型拼接扩展名，反之使用默认扩展名
                    if($suffix != null){
                        if(preg_match($_regular, $suffix)){
                            $_suffix = $suffix;
                        }else{
                            $_suffix = preg_replace($_danger, $_replace, $suffix);
                        }
                    }
                }else{
                    # 当文件类型符合作用域要求时，使用文件类型拼接扩展名
                    if($type !== null){
                        if(array_key_exists($type, $_array)){
                            $type = $_array[$type];
                        }
                        $_suffix = '.'.$type.$_suffix;
                    }
                }
            }
            # 判断完整文件路径是否存在，存在时，直接引入文件，反之抛出异常信息
            if(is_file($_folder.$_file.$_suffix)){
                $_receipt = include($_folder.$_file.$_suffix);
            }else{
                # 异常提示:文件加载失败
                if($throws != 'disabled') {
                    try {
                        throw new Exception('Origin Method Error[1002]: File ' . $_folder . $_file . $_suffix . ' loading failure');
                    } catch (Exception $e) {
                        echo($e->getMessage());
                        exit();
                    }
                }
            }
        }else{
            # 异常提示：文件引导地址无效
            if($throws != 'disabled') {
                try {
                    throw new Exception('Origin Method Error[1003]: Direct address ' . $guide . ' is invalid');
                } catch (Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
        }
    }else{
        # 异常提示：无法引入空地址文件
        if($throws != 'disabled'){
            try{
                throw new Exception('Origin Method Error[1005]: Unable to introduce empty address file');
            }catch(Exception $e){
                echo($e->getMessage());
                exit();
            }
        }
    }
    if(!is_array($_receipt)){
        $_receipt = null;
    }
    return $_receipt;
}
/**
 * 内核控制器加载函数
 * @access public
 * @param string $guide 应用调用地址使用 ：（冒号）作为连接符，
 * 内核控制器地址：Controller:* / 不写，
 * 应用控制器：Application:*，
 * 函数地址：Function:*
 * @return null
 */
function Import($guide)
{
    /**
     * @var array $_array
     * @var string $_url
     */
    $_receipt = null;
    if(strpos($guide,':')){
        $_array = explode(':', $guide);
        if($_array[0] == 'Application'){
            $_url = str_replace(SLASH,':',RING).$guide;
            Hook($_url, 'controller');
        }
        elseif($_array[0] == 'Function'){
            $_url = str_replace(SLASH,':',RING).'Method:Common:'.$_array[1];
            Hook($_url,'function');
        }
        elseif($_array[0] == 'Config'){
            $_url = str_replace(SLASH,':',RING).$guide;
            $_receipt = Hook($_url, null, null, 'config');
        }
        elseif($_array[0] == 'Interface'){
            array_shift($_array);
            $guide = implode(':', $_array);
            $_url = str_replace(SLASH,':',RING).'Kernel:'.$guide;
            Hook($_url,'interface');
        }
        else{
            $_url = str_replace(SLASH,':',RING).'Kernel:'.$guide;
            Hook($_url,'controller');
        }
    }
    return $_receipt;
}
/**
 * 应用环境公共文件引入函数
 * @param $guide
 * @return null;
*/
function Common($guide)
{
    $_receipt = null;
    if(strpos($guide,':')){
        $_url = str_replace(SLASH,':',str_replace('/', SLASH, Config('ROOT_APPLICATION'))).$guide;
        $_obj = explode(':', $guide);
        $_receipt = Hook($_url, strtolower($_obj[0]));
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
    $_regular = '/^[A-Za-z0-9]+([\_\:\\]{1}[A-Za-z0-9]+)*$/';
    $_receipt = false;
    if(preg_match_all($_regular, $param)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * 比较逻辑运算符双向转化方法
 * @access public
 * @param string $symbol
 * @return string
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
    $_temp = file_get_contents($model);
    $_temp = str_replace('{$time}', htmlspecialchars(trim($time)), $_temp);
    $_temp = str_replace('{$message}', htmlspecialchars(trim($message)), $_temp);
    $_temp = str_replace('{$url}', htmlspecialchars(trim($url)), $_temp);
    echo($_temp);
    exit();
}
# 加载函数封装类
Import('File:File'); # 文件控制类
Import('Parameter:Request'); # 调用请求控制器
Import('Parameter:Validate'); # 调用验证控制器
Import('Parameter:Filter');
# 基础操作方法包应用
Import('Function:Validate'); # 引入Validate(验证器)操作函数包
Import('Function:Request'); # 引入Request(请求器)操作函数包
Import('Function:Session'); # 引入session操作函数包
Import('Function:Cookie'); # 引入cookie操作函数包
# 应用公共函数文件
Import('Function:Public'); # 引入单字母方法函数包
Import('Function:Log'); # 引用日志函数包
Import('File:Upload'); # 文件上传控制类
Import('Data:'.C('DATA_TYPE')); # 调用数据库对象组件
Import('Mark:Label'); # 调用标签解析器控制类
Import('Export:Verify'); # 调用验证码组件
Import('Graph:View'); # 调用界面模板匹配控制类
# 引入路由控制函数包
Import('Protocol:Route'); # 调用路由控制函数包
# 应用结构包调用
Common('Common:Public'); # 引入公共函数包
# 公共控制器文件
Import('Application:Controller');
# 动态加载文件
Import('Function:Entrance'); # 引入入口文件包
# 公共资源信息
