<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/20
 * Time: 0:17
 */

namespace Origin\Kernel;

class Entrance
{
    /**
     * 获取对象类信息
     * @var string $_Class
    */
    static $_Class = null;
    /**
     * 获取对象方法信息
     * @var string $_Function
    */
    static $_Function = null;
    /**
     * 自动化加载路口文件
     * @access public
     * @return null
     */
    static function starting()
    {
        /**
         * 使用请求器和验证结构进行入口保护
         * @var string $_class 带命名空间信息的类信息
         * @var string $_object 类实例化对象
         * @var string $_method 类对象方法
         */
        # 根据配置路由模式，调用不同的路由解析模块
        switch(Config('ROUTE_TYPE')){
            case 'developer':
                self::developer(); // 调用开发者模式
                break;
            case 'default':
            default:
                self::path(); //调用路径模式
                break;
        }
        return null;
    }
    /**
     * 默认模式，自动加载入口
     * @access public
     * @return null
     */
    static function path()
    {
        /**
         * 使用请求器和验证结构进行入口保护
         * @var string $_class 带命名空间信息的类信息
         * @var string $_object 类实例化对象
         * @var string $_method 类对象方法
         */
        # 判断自动加载方法
        if(function_exists('spl_autoload_register')){
            # 设置基础控制器参数变量
            $_catalogue = Config('DEFAULT_APPLICATION');
            # 默认控制器文件名
            $_files = Config('DEFAULT_CONTROLLER');
            # 默认控制器方法名
            $_method = Config('DEFAULT_METHOD');
            # 转换信息
            $_path_array = array();
            # 获取的路径信息
            $_path = \Origin\Kernel\Protocol\Route::execute($_SERVER['PATH_INFO']);
            # 获取协议信息
            $_protocol = $_SERVER["SERVER_PROTOCOL"];
            # 获取服务软件信息
            $_server = $_SERVER["SERVER_SOFTWARE"];
            # 获取地址完整信息
            $_http = $_SERVER["HTTP_HOST"];
            # 获取请求地址信息
            $_request = $_SERVER["REQUEST_URI"];
            # 获取请求器类型
            $_type = $_SERVER["REQUEST_METHOD"];
            # 获取用户ip
            $_use = $_SERVER["REMOTE_ADDR"];
            # 调用日志结构函数
            accessLogs("[".$_protocol."] [".$_server."] [Request:".$_type."] to ".$_http.$_request.", by user IP:".$_use);
            # 重定义指针， 起始位置0
            $_i = 0;
            if(!empty($_path) and $_path != null){
                # 转化路径为数组结构
                $_path_array = explode('/',$_path);
                # 判断首元素结构是否与默认应用目录相同
                if($_path_array[$_i] != Config('DEFAULT_APPLICATION') and is_dir(str_replace('/', SLASH, ROOT.Config('ROOT_APPLICATION').$_path_array[0]))){
                    # 变更应用文件夹位置
                    $_catalogue = $_path_array[$_i].SLASH;
                    # 指针下移
                    $_i += 1;
                    if($_i < count($_path_array)){
                        # 变更控制文件信息
                        $_files = $_path_array[$_i];
                        # 指针下移
                        $_i += 1;
                    }
                }else{
                    # 变更控制文件信息
                    $_files = $_path_array[$_i];
                    # 指针下移
                    $_i += 1;
                }
            }
            # 公共方法包引导地址
            $_func_guide = str_replace(SLASH, ':', str_replace('/', SLASH, Config('ROOT_APPLICATION').$_catalogue.'Common/Public'));
            # 使用钩子模型引入方法文件
            Loading($_func_guide,Config('METHOD_SUFFIX'),'disable');
            # 根据配置信息拼接控制器路径
            $_path = $_catalogue.Config('APPLICATION_CONTROLLER').$_files;
            # 设置引导地址
            set_include_path(ROOT);
            # 判断文件是否存在
            if(is_file(str_replace('/', SLASH, Config('ROOT_APPLICATION').$_path.CLASS_SUFFIX))){
                # 使用预注册加载函数，实现自动化加载
                # 使用自动加载，实际过程中，会自动补全当前项目应用程序控制器根目录到控制器描述信息之间缺省部分
                spl_autoload_register(function($_path){
                    require_once(str_replace('/', SLASH, $_path.CLASS_SUFFIX));
                });
            }else{
                try {
                    throw new \Exception('Origin Method Error: Not Fount Control Document');
                }catch(\Exception $e){
                    echo($e->getMessage());
                    exit(0);
                }
            }
            # 创建class完整信息变量
            $_class = str_replace('/', '\\',Config('ROOT_NAMESPACE').SLASH.$_path);
            self::$_Class = $_class;
            # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
            if(class_exists($_class)){
                # 声明类对象
                $_object = new $_class();
            }else{
                try {
                    throw new \Exception('Origin Method Error: Not Fount Control Class');
                }catch(\Exception $e){
                    echo($e->getMessage());
                    exit(0);
                }
            }
            # 判断是否有方法标记信息
            if($_path_array[$_i]){
                # 如果判断标记信息，是否为控制中方法名
                if(method_exists($_object, $_path_array[$_i])){
                    $_method = $_path_array[$_i];
                }
            }
            self::$_Function = $_method;
            # 判断方法信息是否可以被调用
            if(method_exists($_object, $_method) and is_callable(array($_object, $_method))){
                # 执行方法调用
                $_object->$_method();
            }else{
                try {
                    throw new \Exception('Origin Method Error: Not Fount Function Object');
                }catch(\Exception $e){
                    echo($e->getMessage());
                    exit(0);
                }
            }
        }
        return null;
    }
    /**
     * 开发模式，自动加载入口
     * @access public
     * @return null;
     */
    static function developer()
    {
        /**
         * 使用请求器和验证结构进行入口保护
         * @var string $_class 带命名空间信息的类信息
         * @var string $_object 类实例化对象
         * @var string $_method 类对象方法
         */
        # 判断自动加载方法
        if(function_exists('spl_autoload_register')){
            # 使用预加载方法，动态加载控制器载入方法
            /**
             * @var string $_path 控制完整路径信息
             */
            # 根据配置信息拼接控制器路径
            $_path = __APPLICATION__.Configurate('APPLICATION_CONTROLLER').SLASH.Request('GET.'.Config('RETRIEVER_CLASS'), Config('DEFAULT_CONTROLLER')).Config('SECOND_NAME');
            # 设置引导地址
            set_include_path(ROOT);
            # 判断文件是否存在
            if(is_file(str_replace('/', SLASH, Config('ROOT_APPLICATION').$_path.CLASS_SUFFIX))){
                # 使用预注册加载函数，实现自动化加载
                spl_autoload_register(function($_path){
                    require_once(str_replace('/', SLASH, Config('ROOT_APPLICATION').$_path.CLASS_SUFFIX));
                });
            }else{
                try {
                    throw new \Exception('Origin Method Error: Not Fount Control Document');
                }catch(\Exception $e){
                    echo($e->getMessage());
                    exit(0);
                }
            }
            # 删除预设控制参数信息
            Request('GET.'.Config('RETRIEVER_CLASS'), 'delete');
            # 创建控制器名变量
            $_class = str_replace('/', '\\',Config('ROOT_NAMESPACE').SLASH.$_path);
            # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
            if(class_exists($_class)){
                # 声明类对象
                $_object = new $_class();
            }else{
                try {
                    throw new \Exception('Origin Method Error: Not Fount Control Class');
                }catch(\Exception $e){
                    echo($e->getMessage());
                    exit(0);
                }
            }
            # 判断是否有方法标记信息
            if(Request('GET.'.Config('RETRIEVER_METHOD'))){
                # 如果判断标记信息，是否为控制中方法名
                if(method_exists($_object, Request('GET.'.Config('RETRIEVER_METHOD')))){
                    $_method = Request('GET.'.Config('RETRIEVER_METHOD'));
                }else{
                    $_method = Config('DEFAULT_METHOD');
                }
                # 删除预设方法参数信息
                Request('GET.'.Config('RETRIEVER_METHOD'), 'delete');
            }else{
                $_method = Config('DEFAULT_METHOD');
            }
            # 判断方法信息是否可以被调用
            if(method_exists($_object, $_method) and is_callable(array($_object, $_method))){
                # 执行方法调用
                $_object->$_method();
            }else{
                try {
                    throw new \Exception('Origin Method Error: Not Fount Function Object');
                }catch(\Exception $e){
                    echo($e->getMessage());
                    exit(0);
                }
            }
        }
        return null;
    }
}