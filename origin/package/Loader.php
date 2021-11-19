<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context Origin自动加载封装类
 */
namespace Origin\Package;

use Exception;

class Loader
{
    /**
     * @access public
     * @static
     * @var string $Class 访问对象类名称
     */
    static $Class = null;

    /**
     * @access public
     * @static
     * @var string $Function 访问对象函数名称
     */
    static $Function = null;

    /**
     * @access public
     * @static
     * @var float $LoadTime 访问时间戳
    */
    static $LoadTime = 0.0;

    /**
     * 默认模式，自动加载入口
     * @access public
     * @return void
     */
    static function initialize()
    {
        # 应用结构包调用
        if(is_file($common = replace(ROOT . "/application/common/public.php"))) include("$common");
        # 运行起始时间
        self::$LoadTime = explode(" ",microtime());
        self::$LoadTime = floatval(self::$LoadTime[0])+floatval(self::$LoadTime[1]);
        if(DEBUG) initialize(); # 初始化应用结构内容
        # 判断自动加载方法
        if(function_exists('spl_autoload_register')){
            # 设置基础控制器参数变量
            $catalogue = DEFAULT_APPLICATION;
            # 默认控制器方法名
            $method = DEFAULT_FUNCTION;
            # 获取的路径信息
            if(is_null($_SERVER['PATH_INFO']) or empty($_SERVER['PATH_INFO']))
                $path = self::route($_SERVER["REQUEST_URI"]);
            else
                $path = self::route($_SERVER['PATH_INFO']); // nginx条件下PATH_INFO返回值为空
            # 路由返回值为数组
            if(is_array($path)){
                $function = $path[1];
                $path = $path[0];
            }
            # 获取协议信息
            $protocol = $_SERVER["SERVER_PROTOCOL"];
            # 获取服务软件信息
            $server = $_SERVER["SERVER_SOFTWARE"];
            # 获取地址完整信息
            $http = $_SERVER["HTTP_HOST"];
            # 获取请求地址信息
            $request = $_SERVER["REQUEST_URI"];
            # 获取请求器类型
            $type = $_SERVER["REQUEST_METHOD"];
            # 获取用户ip
            $use = $_SERVER["REMOTE_ADDR"];

            # 对请求对象地址请求内容进行截取
            if(strpos($request,'?'))
                $request = substr($request,0,strpos($request,'?'));
            # 执行初始化
            # 判断执行对象是否为程序单元
            $bool = false;
            $suffix = array(".html",".htm",".php");
            for($i = 0;$i < count($suffix);$i++){
                if(!empty(strpos($request,$suffix[$i]))){
                    $bool = true;
                    break;
                }
            }
            # 初始化对象及路径变量
            $class_path = null;
            $class_namespace = null;
            # 忽略其他资源类型文件索引
            if(!$bool)
                if(strpos($request,".") === false) $bool = true;
            if($bool){
                # 重定义指针， 起始位置0
                if(!empty($path) and $path != "/"){
                    # 转化路径为数组结构
                    $path_array = explode('/',strtolower($path));
                    $i = 0;
                    # 循环路径数组
                    for(;$i < count($path_array);$i++){
                        $symbol = null;
                        $namespace_symbol = null;
                        if(!is_null($class_path)){
                            $symbol = DS;
                            $namespace_symbol = "\\";
                        }
                        if(is_file(ROOT.$class_path.$symbol."classes".DS.ucfirst($path_array[$i]).".php")){
                            $class_path .= $symbol."classes".DS.ucfirst($path_array[$i]).".php";
                            $class_namespace .= $namespace_symbol."Classes\\".ucfirst($path_array[$i]);
                            $class = ucfirst($path_array[$i]);
                            break;
                        }
                        if(is_dir(ROOT.$class_path.$symbol.$path_array[$i])){
                            $class_path .= $symbol.$path_array[$i];
                            $class_namespace .= $namespace_symbol.ucfirst($path_array[$i]);
                            if($path_array[$i] === "application")
                                $catalogue = $path_array[$i + 1];
                            continue;
                        }
                        if(is_dir(ROOT.DS."application".DS.$path_array[$i])){
                            $class_path .= DS."application".DS.$path_array[$i];
                            $class_namespace .= $namespace_symbol."Application\\".ucfirst($path_array[$i]);
                            $catalogue = $path_array[$i];
                            continue;
                        }
                        if(isset($function) and $i === (count($path_array) - 1)
                            and is_file(ROOT.$class_path.$symbol.ucfirst($path_array[$i]).".php")){
                            $class_path .= $symbol.ucfirst($path_array[$i]).".php";
                            $class_namespace .= $namespace_symbol.ucfirst($path_array[$i]);
                            $class = ucfirst($path_array[$i]);
                            break;
                        }
                        if(is_file(ROOT.DS."application".DS.DEFAULT_APPLICATION.DS."classes".DS.ucfirst($path_array[$i]).".php")){
                            $class_path = replace("application/$catalogue/classes/".ucfirst($path_array[$i]).".php");
                            $class_namespace = "Application\\".ucfirst(DEFAULT_APPLICATION)."\\Classes\\".ucfirst($path_array[$i]);
                            $class = ucfirst(ucfirst($class_path[$i]));
                            break;
                        }
                    }
                    if(!isset($class)){
                        $class_path .= replace("/classes/".ucfirst(DEFAULT_CLASS).".php");
                        $class_namespace .= "\\Classes\\".ucfirst(DEFAULT_CLASS);
                    }
                    if(!isset($function)){
                        if($i < (count( $path_array) -1))
                            $method = $path_array[$i+1];
                    }else
                        $method = $function;
                }else{
                    $class_path = replace("application/$catalogue/classes/".ucfirst(DEFAULT_CLASS).".php");
                    $class_namespace = "Application\\".ucfirst(DEFAULT_APPLICATION)."\\Classes\\".ucfirst(DEFAULT_CLASS);
                }
                # 使用加载函数引入应用公共方法文件
                if(is_file($public = replace(ROOT."/application/$catalogue/common/public.php")))
                    include("$public");
                # 初始化重启位置
                load:
                # 验证文件地址是否可以访问
                if(!isset($class_path) or !is_file(ROOT.DS.$class_path)){
                    if(DEBUG){
                        try {
                            throw new Exception('Origin Loading Error: Not Fount Classes Document');
                        } catch (Exception $e) {
                            self::error(replace("$class_path.php"), $e->getMessage(), "File");
                            exit(0);
                        }
                    }else{
                        $_404 = replace(ROOT_RESOURCE."/public/template/404.html");
                        if(!is_file($_404)){
                            echo("ERROR:404");
                            exit();
                        }else{
                            include("$_404");
                        }
                    }
                }
                # 调用自动加载函数
                self::autoload($class_path);
                # 链接记录日志
                $uri = LOG_ACCESS.date('Ymd').'.log';
                $msg = "[".$protocol."] [".$server."] [Request:".$type."] to ".$http.$request.", by user IP:".$use;
                $model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
                _log($uri,$model_msg);
                # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
                if(class_exists($class_namespace)){
                    self::$Class = $class_namespace;
                    # 声明类对象
                    $object = new $class_namespace();
                }else{
                    try {
                        throw new Exception('Origin Loading Error: Not Fount Control Class');
                    }catch(Exception $e){
                        self::error("$class_namespace",$e->getMessage(),"Class");
                        exit(0);
                    }
                }
                # 判断方法信息是否可以被调用
                if(method_exists($object, $method) and is_callable(array($object, $method))){
                    self::$Function = $method;
                    # 执行方法调用
                    $object->$method();
                }else{
                    try {
                        throw new Exception('Origin Loading Error: Not Fount Function Object');
                    } catch (Exception $e) {
                        self::error("$method", $e->getMessage(), "Function");
                        exit(0);
                    }
                }
            }
        }
    }

    /**
     * 路由解析函数
     * @access protected
     * @param string $uri 路由对象地址
     * @return array|string 返回路由信息
    */
    protected static function route(string $uri){
        # 创建回执变量
        $receipt = null;
        # 配置文件地址
        $config = replace(ROOT."/common/config/route.php");
        # 获取路由列表信息
        $configure = include("$config");
        # 循环比对路由信息
        for($i = 0;$i < count($configure);$i++){
            # 判断路由内容数据类型(string|array)
            if(key_exists("mapping",$configure[$i])){
                $config= array_change_key_case($configure[$i]);
                if(is_array($config["mapping"])){
                    $config["mapping"] = array_change_value_case($config["mapping"]);
                    if(!in_array($uri,$config["mapping"])) continue;
                }elseif(strtolower($config["mapping"]) != strtolower($uri)) continue;
                if(key_exists("method",$config) and $config["method"] != "normal")
                    if(strtoupper($config["method"]) != $_SERVER["REQUEST_METHOD"]) break;
                if(key_exists("classes",$config) and key_exists("functions",$config)){
                    $receipt = array($config["classes"],$config["functions"]);
                    break;
                }else break;
            }
        }
        End:
        if(is_null($receipt)){
            $start = 0;
            if(strpos($uri,"/") == 0)
                $start = 1;
            if(strpos($uri,'.'))
                $path = substr($uri, $start, strpos($uri,'.')-1);
            else
                $path = substr($uri, $start);
            $receipt = $path;
        }
        # 返回回执内容
        return $receipt;
    }

    /**
     * 自动加载模块
     * @access protected
     * @param string $file 文件地址
     * @return void
    */
    protected static function autoload(string $file)
    {
        # 设置引导地址
        set_include_path(ROOT);
        # 判断文件是否存在
        if(!spl_autoload_register(function($file){
            # 转化命名空间内容，拆分结构
            $file = explode("\\",$file);
            # 循环修改命名空间元素首字母
            for($i = 0;$i < count($file);$i++){
                # 修改文件名,类文件名跳过
                if($i === (count($file) - 1))
                    continue;
                $file[$i] = strtolower($file[$i]);
            }
            # 重组加载信息内容
            $file = implode(DS,$file);
            require_once("$file.php");
        })){
            try {
                throw new Exception('Origin Loading Error: Registration load failed');
            } catch (Exception $e) {
                self::error(replace("$file.php"), $e->getMessage(), "File");
                exit(0);
            }
        }
    }

    /**
     * 加载错误信息
     * @access public
     * @param string $obj 未加载对象（class|function）
     * @param string $error 错误信息
     * @param string $type 加载类型
     * @return void
     */
    static function error(string $obj, string $error, string $type)
    {
        if(DEBUG or ERROR){
            if(!is_file($_404 = replace(RESOURCE_PUBLIC."/template/404.html"))){
                $_404 = replace(ORIGIN.'template/404.html');
            }
            include("$_404");
            if($obj) unset($obj);
            if($error) unset($error);
            if($type) unset($type);
            exit(0);
        }
    }
}