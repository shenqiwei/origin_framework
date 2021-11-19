<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin公共控制器
 */
namespace Origin\Package;

use Exception;

/**
 * 功能控制器，负责内核功能方法的预加载调用
*/
abstract class Unit
{
    /**
     * 请求器类型约束常量 */
    const REQUEST_GET = "get"; # get 请求类型
    const REQUEST_POST = "post"; # post 请求类型

    /**
     * @access private
     * @var array $Param 装载参数信息数组
    */
    private $Param = array();

    /**
     * 构造方法，获取当前操作类信息
     * @access public
     * @return void
    */
    function __construct()
    {}

    /**
     * 向模板加载数据信息
     * @access protected
     * @param string $key 对象键
     * @param mixed $value 接入值
     * @return void
    */
    protected function param($key, $value)
    {
        $regular = '/^[^\_\W]+(\_[^\_\W]+)*$/';
        if(is_true($regular, $key)){
            $this->Param[Loader::$Class][$key] = $value;
        }else{
            # 异常提示：变量名称包含非合法符号
            try{
                throw new Exception('Variable name contains non legal symbols');
            }catch(Exception $e){
                errorLog($e->getMessage());
                exception("Param Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
    }

    /**
     * 调用模板方法
     * @access protected
     * @param string|null $template 视图模板
     * @return void
     */
    protected function template($template=null)
    {
        $page = Loader::$Function;
        $regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
        $dir = str_replace("classes/", '',
                str_replace(DEFAULT_APPLICATION."/", '',
                    str_replace('application/', '',
                        str_replace('\\', '/', strtolower(Loader::$Class)))));
        if(!is_null($template) and is_true($regular, $template)){
            $page = $template;
        }
        View::view($dir, $page,$this->Param[Loader::$Class],Loader::$LoadTime);
    }

    /**
     * 返回执行对象类名
     * @access protected
     * @return string 返回类名
     */
    protected function get_class()
    {
        return Loader::$Class;
    }

    /**
     * 返回执行对象方法名
     * @access protected
     * @return string 返回方法名称
     */
    protected function get_function()
    {
        return Loader::$Function;
    }

    /**
     * 执行成功提示信息
     * @access protected
     * @param string $message
     * @param string $url
     * @param int $time
     * @return void
    */
    protected function success($message='success',$url='#',$time=3)
    {
        $setting = array("bgcolor"=>"floralwhite","color"=>"#000000","title"=>"Success");
        Output::output($time, $message, $url, $setting);
    }

    /**
     * 错误提示
     * @access protected
     * @param string $message
     * @param string $url
     * @param int $time
     * @return void
    */
    protected function error($message='error',$url='#',$time=3)
    {
        $setting = array("bgcolor"=>"orangered","color"=>"floralwhite","title"=>"Error");
        Output::output($time, $message, $url, $setting);
    }

    /**
     * 地址跳转（重定向）
     * @access protected
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        header("Location:{$url}");
    }

    /**
     * json格式输出
     * @access protected
     * @param array $array
     * @return void
    */
    protected function json($array)
    {
        Output::json($array);
    }

    /**
     * 控制器默认加载函数
     * @access public
     * @return void
    */
    public abstract function index();
}