<?php
/**
 *  coding: utf-8  * 
 *  system OS: windows2008  * 
 *  work Tools:Phpstorm  * 
 *  language Ver: php7.1  * 
 *  agreement: PSR-1 to PSR-11  * 
 *  filename: IoC.Origin.Function.function * 
 *  version: 0.1 * 
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 */
# 初试公共函数包
include('Common/Common.func.php');
include('Common/Hook.func.php');
include('Common/Config.func.php');
# 框架柱目录文件路径
if(!defined('RING')) define('RING', 'Origin'.SLASH);
# 公共配置常量
if(!defined('CLASS_SUFFIX')) define('CLASS_SUFFIX', Configuration('CLASS_SUFFIX'));
if(!defined('METHOD_SUFFIX')) define('METHOD_SUFFIX', Configuration('METHOD_SUFFIX'));
if(!defined('CONFIG_SUFFIX')) define('CONFIG_SUFFIX', Configuration('CONFIG_SUFFIX'));
# 判断程序是否锁定域名
if(Configuration('URL_HOST_ONLY') != 0){
    # 判断访问地址与注册域名是否相同
    if(Configuration('URL_HOST') != $_SERVER['HTTP_HOST']){
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
if(!defined('__APPLICATION__')) define('__APPLICATION__', Configuration('DEFAULT_APPLICATION'));
# 协议类型
if(!defined('__PROTOCOL__')) define('__PROTOCOL__', $_SERVER['HTTPS']? 'https://' : 'http://');
# 地址信息
if(!defined('__HOST__')) define('__HOST__',__PROTOCOL__.$_SERVER['HTTP_HOST'].'/');
# 插件应用常量
if(!defined('__PLUGIN__')) define('__PLUGIN__', Configuration('ROOT_PLUGIN'));
# 资源应用常量
if(!defined('__RESOURCE__')) define('__RESOURCE__', Configuration('ROOT_RESOURCE'));

# 加载函数封装类
Import('File:File'); # 文件控制类
Import('Parameter:Request'); # 调用请求控制器
Import('Parameter:Validate'); # 调用验证控制器
Import('Parameter:Filter');

# 基础操作方法包应用
include('Common/File.func.php');
include('Common/Request.func.php');
include('Common/Validate.func.php');
include('Common/Filter.func.php');
include('Common/Session.func.php');
include('Common/Cookie.func.php');
# 应用公共函数文件
include('Common/Log.func.php'); # 引用日志函数包
include('Common/Public.func.php'); # 文件操作函数包
# 公共应用函数类
Import('File:Upload'); # 文件上传控制类
Import('Data:Mysql'); # 调用Mysql数据库对象组件
Import('Data:Redis'); # 调用Redis数据库对象组件
Import("Data:Mongodb"); # 调用MongoDB 70+支持包
Import('Interface:Mark:Impl:Label'); # 调用内建标签解释结构接口控制类
Import('Mark:Label'); # 调用标签解析器控制类
Import('Export:Verify'); # 调用验证码组件
Import('Graph:View'); # 调用界面模板匹配控制类
Import('Parameter:Output'); # 调用数据结构输出控制类
# 引入路由控制函数包
Import('Protocol:Route'); # 调用路由控制函数包
Import('Protocol:Curl'); # 调用远程请求函数包
/**
 * Common 有行能bug内容，关系结构Config需要进行结构简化和重定义
*/
# 应用结构包调用
Common('Common:Public'); # 引入公共函数包
# 公共控制器文件
Import('Application:Controller');
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
    define('E_FATAL',  E_ERROR | E_USER_ERROR |  E_CORE_ERROR |
        E_COMPILE_ERROR | E_RECOVERABLE_ERROR| E_PARSE );
    if($_error && ($_error["type"]===($_error["type"] & E_FATAL))) {
        if(DEBUG){
            $_debug = new  \Origin\Kernel\Parameter\Output();
            $_debug->base($_error);
        }
    }
    return null;
}
# 动态加载文件
include('Common/Entrance.func.php'); # 引入入口文件包
// 调用方法体
Entrance();