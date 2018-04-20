<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Function.Common.Entrance *
 * version: 0.1 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context:
 * create Time: 2017/01/09 15:34
 * update Time: 2017/01/09 15:34
 * IoC 自动加载方法函数
 */
/**
 * 自动化加载路口文件
 * @access public
 * @return null
*/
function Entrance()
{
    /**
     * 使用请求器和验证结构进行入口保护
     * @var string $_class 带命名空间信息的类信息
     * @var string $_object 类实例化对象
     * @var string $_method 类对象方法
    */
    # 根据配置路由模式，调用不同的路由解析模块
    switch(C('ROUTE_TYPE')){
        case 'developer':
            developer(); // 调用开发者模式
            break;
        case 'default':
        default:
            path(); //调用路径模式
            break;
    }
    return null;
}
/**
 * 默认模式，自动加载入口
 * @access public
 * @return null
*/
function path()
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
        $_catalogue = C('DEFAULT_APPLICATION');
        # 默认控制器文件名
        $_files = C('DEFAULT_CONTROLLER');
        # 默认控制器类名，由于规则规定类名与文件一致，所以该结构暂时只作为平行结构来使用
        # $_class = Config('DEFAULT_CONTROLLER');
        # 默认控制器方法名
        $_method = C('DEFAULT_METHOD');
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
            if($_path_array[$_i] != C('DEFAULT_APPLICATION') and is_dir(str_replace('/', SLASH, ROOT.C('ROOT_APPLICATION').$_path_array[0]))){
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
        $_func_guide = str_replace(SLASH, ':', str_replace('/', SLASH, C('ROOT_APPLICATION').$_catalogue.'Common/Public'));
        # 使用钩子模型引入方法文件
        Hook($_func_guide,C('METHOD_SUFFIX'),'disable');
        # 根据配置信息拼接控制器路径
        $_path = $_catalogue.C('APPLICATION_CONTROLLER').$_files;
        # 设置引导地址
        set_include_path(ROOT);
        # 判断文件是否存在
        if(is_file(str_replace('/', SLASH, C('ROOT_APPLICATION').$_path.CLASS_SUFFIX))){
            # 使用预注册加载函数，实现自动化加载
            # 使用自动加载，实际过程中，会自动补全当前项目应用程序控制器根目录到控制器描述信息之间缺省部分
            spl_autoload_register(function($_path){
                require_once(str_replace('/', SLASH, $_path.CLASS_SUFFIX));
            });
        }else{
            try {
                throw new Exception('Origin Method Error: Not Fount Control Document');
            }catch(Exception $e){
                echo($e->getMessage());
                exit(0);
            }
        }
        # 创建class完整信息变量
        $_class = str_replace('/', '\\',C('ROOT_NAMESPACE').SLASH.$_path);
        # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
        if(class_exists($_class)){
            # 声明类对象
            $_object = new $_class();
        }else{
            try {
                throw new Exception('Origin Method Error: Not Fount Control Class');
            }catch(Exception $e){
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
        # 判断方法信息是否可以被调用
        if(method_exists($_object, $_method) and is_callable(array($_object, $_method))){
            # 执行方法调用
            $_object->$_method();
        }else{
            try {
                throw new Exception('Origin Method Error: Not Fount Function Object');
            }catch(Exception $e){
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
function developer()
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
        $_path = __APPLICATION__.Config('APPLICATION_CONTROLLER').SLASH.Request('GET.'.C('RETRIEVER_CLASS'), C('DEFAULT_CONTROLLER')).C('SECOND_NAME');
        # 设置引导地址
        set_include_path(ROOT);
        # 判断文件是否存在
        if(is_file(str_replace('/', SLASH, C('ROOT_APPLICATION').$_path.CLASS_SUFFIX))){
            # 使用预注册加载函数，实现自动化加载
            spl_autoload_register(function($_path){
                require_once(str_replace('/', SLASH, C('ROOT_APPLICATION').$_path.CLASS_SUFFIX));
            });
        }else{
            try {
                throw new Exception('Origin Method Error: Not Fount Control Document');
            }catch(Exception $e){
                echo($e->getMessage());
                exit(0);
            }
        }
        # 删除预设控制参数信息
        Request('GET.'.C('RETRIEVER_CLASS'), 'delete');
        # 创建控制器名变量
        $_class = str_replace('/', '\\',C('ROOT_NAMESPACE').SLASH.$_path);
        # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
        if(class_exists($_class)){
            # 声明类对象
            $_object = new $_class();
        }else{
            try {
                throw new Exception('Origin Method Error: Not Fount Control Class');
            }catch(Exception $e){
                echo($e->getMessage());
                exit(0);
            }
        }
        # 判断是否有方法标记信息
        if(Request('GET.'.C('RETRIEVER_METHOD'))){
            # 如果判断标记信息，是否为控制中方法名
            if(method_exists($_object, Request('GET.'.C('RETRIEVER_METHOD')))){
                $_method = Request('GET.'.C('RETRIEVER_METHOD'));
            }else{
                $_method = C('DEFAULT_METHOD');
            }
            # 删除预设方法参数信息
            Request('GET.'.C('RETRIEVER_METHOD'), 'delete');
        }else{
            $_method = C('DEFAULT_METHOD');
        }
        # 判断方法信息是否可以被调用
        if(method_exists($_object, $_method) and is_callable(array($_object, $_method))){
            # 执行方法调用
            $_object->$_method();
        }else{
            try {
                throw new Exception('Origin Method Error: Not Fount Function Object');
            }catch(Exception $e){
                echo($e->getMessage());
                exit(0);
            }
        }
    }
    return null;
}
// 调用方法体
Entrance();