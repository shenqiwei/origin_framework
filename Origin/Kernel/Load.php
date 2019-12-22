<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 * @context 自动加载封装类
 */
namespace Origin\Kernel;

use Exception;

class Load
{
    /**
     * @access public
     * @static
     * @var $_Class
     * @var $_Function
    */
    public static $_Class = null;
    public static $_Function = null;
    /**
     * 默认模式，自动加载入口
     * @access public
     * @return null
     */
    static function initialize()
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
            $_catalogue = Config('DEFAULT_APPLICATION')."/";
            # 默认控制器文件名
            $_files = Config('DEFAULT_CONTROLLER');
            # 默认控制器类名，由于规则规定类名与文件一致，所以该结构暂时只作为平行结构来使用
            # $_class = Configuration('DEFAULT_CONTROLLER');
            # 默认控制器方法名
            $_method = Config('DEFAULT_METHOD');
            # 转换信息
            $_path_array = array();
            # 获取的路径信息
            $_path = self::route($_SERVER['PATH_INFO']);
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
            # 对请求对象地址请求内容进行截取
            if(strpos($_request,'?'))
                $_request = substr($_request,0,strpos($_request,'?'));
            # 执行初始化
            # 判断执行对象是否为程序单元
            $_bool = false;
            $_suffix = array(".html",".htm",".php");
            for($_i = 0;$_i < count($_suffix);$_i++){
                if(!empty(strpos($_request,$_suffix[$_i]))){
                    $_bool = true;
                    break;
                }
            }
            # 忽略其他资源类型文件索引
            if(!$_bool)
                if(strpos($_request,".") === false) $_bool = true;
            if($_bool){
                # 重定义指针， 起始位置0
                $_i = 0;
                if(!empty($_path) and $_path != null){
                    # 转化路径为数组结构
                    $_path_array = explode('/',$_path);
                    # 判断首元素结构是否与默认应用目录相同
                    if(empty($_path_array) and $_path_array[0] != '0' or
                        ucfirst($_path_array[$_i]) == Config('DEFAULT_APPLICATION')  or
                        (ucfirst($_path_array[$_i]) != Config('DEFAULT_APPLICATION') and
                            is_dir(str_replace('/', DS, ROOT."Application/".ucfirst($_path_array[0]))))) {
                        # 变更应用文件夹位置
                        $_catalogue = ucfirst($_path_array[$_i]) . DS;
                        # 指针下移
                        $_i += 1;
                        if ($_i < count($_path_array)) {
                            # 变更控制文件信息
                            $_files = ucfirst($_path_array[$_i]);
                            # 指针下移
                            $_i += 1;
                        }
                    }else{
                        # 变更控制文件信息
                        $_files = ucfirst($_path_array[$_i]);
                        # 指针下移
                        $_i += 1;
                    }
                }
                # 使用加载函数引入应用公共方法文件
                Import("Application/{$_catalogue}Common/Public");
                # 根据配置信息拼接控制器路径
                $_path = $_catalogue.Config('APPLICATION_CONTROLLER')."/".ucfirst($_files);
                # 设置引导地址
                set_include_path(ROOT);
                Loading:
                # 判断文件是否存在
                if(is_file(str_replace('/', DS, "Application/{$_path}.php"))){
                    # 使用预注册加载函数，实现自动化加载
                    # 使用自动加载，实际过程中，会自动补全当前项目应用程序控制器根目录到控制器描述信息之间缺省部分
                    spl_autoload_register(function($_path){
                        require_once(str_replace('\\',DS,str_replace('/', DS, $_path.'.php')));
                    });
                }else{
                    if(DEBUG){
                        initialize();
                        goto Loading;
                    }else {
                        try {
                            throw new Exception('Origin Method Error: Not Fount Control Document');
                        } catch (Exception $e) {
                            self::error(str_replace('/', DS, "Application/{$_path}.php"), $e->getMessage(), "File");
                            exit(0);
                        }
                    }
                }
                # 链接记录日志
                $_uri = Config('ROOT_LOG').Config('LOG_ACCESS').date('Ymd').'.log';
                $_msg = "[".$_protocol."] [".$_server."] [Request:".$_type."] to ".$_http.$_request.", by user IP:".$_use;
                $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$_msg.PHP_EOL;
                write($_uri,$_model_msg);
                # 创建class完整信息变量
                $_class = str_replace('/', '\\',"Application".DS.$_path);
                # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
                if(class_exists($_class)){
                    self::$_Class = $_class;
                    # 声明类对象
                    $_object = new $_class();
                }else{
                    if(DEBUG){
                        initialize();
                    }else{
                        try {
                            throw new Exception('Origin Method Error: Not Fount Control Class');
                        }catch(Exception $e){
                            self::error("{$_class}",$e->getMessage(),"Class");
                            exit(0);
                        }
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
                    self::$_Function = $_method;
                    # 执行方法调用
                    $_object->$_method();
                }else{
                    if(DEBUG){
                        initialize();
                    }else {
                        try {
                            throw new Exception('Origin Method Error: Not Fount Function Object');
                        } catch (Exception $e) {
                            self::error("{$_method}", $e->getMessage(), "Function");
                            exit(0);
                        }
                    }
                }
            }
        }
        return null;
    }

    static function route($route){
        # 转化路径为数组结构
        $_path = explode('/',trim($route,'/'));
        # 创建对象变量
        $_obj = null;
        # 创建指针变量，起始位置0
        $_i = 0;
        # 循环处理路径参数
        while(true){
            # 判断结构中存在扩展名连接符号
            if(strpos($_path[$_i],'.')){
                # 消除扩展名信息
                $_path[$_i] = substr($_path[$_i], 0, strpos($_path[$_i],'.'));
            }
            # 指针下移
            $_i += 1;
            # 设置跳出结构条件
            if($_i >= count($_path)) break;
        }
        # 创建返回变量
        $_receipt = implode('/', $_path);
        # 创建路由文件目录变量
        $_files = str_replace('/', DS, ROOT."Application/".Configuration('ROUTE_CATALOGUE'));
        # 判断文件是否存在
        if(is_dir($_files)){
            # 判断路由文件是否存在
            if(is_file($_files.Configuration('ROUTE_FILES'))){
                # 获取路由配置信息
                $_obj = require_once($_files.Configuration('ROUTE_FILES'));
            }
        }
        # 判断路径信息是否有效
        if(count($_path) > 0){
            # 判断路由信息是否有效
            if (is_array($_obj) and count($_obj) > 0){
                # 遍历路由信息，用于比对路由信息
                for ($i = 0; $i < count($_obj); $i++){
                    # 判断路由文件是否合法
                    if(!is_array($_obj[$i])){
                        # 异常信息：路由结构配置错误
                        try {
                            throw new Exception('Route Error:Routing structure configuration errors');
                        }catch(Exception $e){
                            self::error("{$route}",$e->getMessage(),"config");
                            exit(0);
                        }
                    }
                    # 匹配路由信息
                    if(strpos($_receipt, $_obj[$i]['route']) === false or strpos($_receipt, $_obj[$i]['route']) > 0) continue;
                    # 截取剩余路由信息(参数信息)
                    $_param = trim(substr($_receipt, strlen($_obj[$i]['route'])),'/');
                    # 获取映射信息
                    $_receipt = $_obj[$i]['mapping'];
                    # 判断是否存在参数段路由
                    if(!empty($_param) and array_key_exists('param', $_obj[$i])){
                        # 遍历路由参数数组
                        for($j = 0; $j < count($_obj[$i]['param']); $j++){
                            # 转化参数路由
                            $_param = explode('/', $_param);
                            # 检查元素结构
                            if(!array_key_exists('name', $_obj[$i]['param'][$j])){
                                # 异常信息：未设置参数名称
                                try {
                                    throw new Exception('Route Error:Not set the parameter name');
                                }catch(Exception $e){
                                    self::error("{$route}",$e->getMessage(),"config");
                                    exit(0);
                                }
                            }
                            # 获取默认值
                            $_default = array_key_exists('default', $_obj[$i]['param'][$j]) ? $_obj[$i]['param'][$j]['default'] : null;
                            # 判断数据类型
                            $_type = array_key_exists('type',$_obj[$i]['param'][$j]) ? $_obj[$i]['param'][$j]['type'] : 'string';
                            switch($_type){
                                case 'int':
                                case 'integer':
                                    echo($_param[$j]);
                                    # 创建get参数对象信息
                                    $_GET[$_obj[$i]['param'][$j]['name']] = ((!empty($_param[$j]) and intval($_param[$j]) != 0 )or $_param[$j] != null) ? intval($_param[$j]) : (is_null($_default)) ? null : intval($_default);
                                    break;
                                case 'float':
                                case 'double':
                                    # 创建get参数对象信息
                                    $_GET[$_obj[$i]['param'][$j]['name']] = (!empty($_param[$j]) or $_param[$j] != null) ? intval($_param[$j]) : (is_null($_default)) ? null : doubleval($_default);
                                    break;
                                case 'boolean':
                                    # 创建get参数对象信息
                                    $_GET[$_obj][$i]['param'][$j]['name'] = ((!empty($_param[$j]) and intval($_param[$j]) != 0 and boolval($_param[$j]) != false )or $_param[$j] != null) ? boolval($_param[$j]) : (is_null($_default)) ? null : boolval($_default);
                                    break;
                                case 'string':
                                default:
                                    # 创建get参数对象信息
                                    $_GET[$_obj[$i]['param'][$j]['name']] = (!empty($_param[$j]) or $_param[$j] != null) ? strval($_param[$j]) : (is_null($_default)) ? null : strval($_default);
                                    break;
                            }
                        }
                    }
                    # 跳出循环结束验证
                    break;
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $obj 未加载对象（class|function）
     * @param string $error 错误信息
     * @param string $type 加载类型
     * @context 加载错误信息
     */
    static function error($obj,$error,$type)
    {
        if(DEBUG or ERROR)
            include(str_replace('/',DS,ROOT.RING.'Template/Load.html'));
    }
}