<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2017
 * @context: IoC 公共控制器
 */
namespace Origin\Application;
/**
 * 功能控制器，负责内核功能方法的预加载调用
*/
class Controller
{
    /**
     * 获取当前操作类名
     * @static string $_Name_Class
    */
    static $_Name_Class = null;
    /**
     * 获取当前执行方法名
     * @static string $_Name_Function
    */
    static $_Name_Function = null;
    /**
     * 装载参数信息数组
     * @var array $_Param_Array
    */
    private $_Param_Array = array();
    /**
     * 构造方法，获取当前操作类信息
     * @access public
    */
    function __construct()
    {
        self::$_Name_Class = get_class($this);
    }
    /**
     * 向模板加载数据信息
     * @access protected
     * @param $key
     * @param $value
     * @return null
    */
    protected function param($key, $value)
    {
        $_method = null;
        $_regular = '/^[^\_\W]+(\_[^\_\W]+)*$/';
        if(is_true($_regular, $key) === true){
            $this->_Param_Array[self::$_Name_Class][$key] = $value;
        }else{
            # 异常提示：变量名称包含非合法符号
            try{
                throw new \Exception('Origin Application Error: Variable name contains non legal symbols');
            }catch(\Exception $e){
                echo($e->getMessage());
                exit();
            }
        }
        return null;
    }
    /**
     * 调用模板方法
     * @access protected
     * @param $view
     * @return null
     */
    protected function view($view=null)
    {
        $_page = null;
        $_method = null;
        $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
        if(is_true($_regular, Config('DEFAULT_VIEW')) === true){
            $_page = Config('DEFAULT_VIEW');
        }
        $_dir = str_replace('Controller', '',
            str_replace(Config('APPLICATION_CONTROLLER')."/", '',
                str_replace(Config('DEFAULT_APPLICATION')."/", '',
                    str_replace('Application/', '',
                        str_replace('\\', '/', self::$_Name_Class)))));
        if(!is_null($this->get_function())){
            $_page = $this->get_function();
        }
        if($view !== null and is_true($_regular, $view) === true){
            $_page = $view;
        }
        $_obj = new \Origin\Kernel\Graph\View($_dir, $_page);
        $_obj->view($this->_Param_Array[self::$_Name_Class]);
        return null;
    }
    /**
     * 返回执行对象类名
     * @access protected
     * @return string
     */
    protected function get_class()
    {
        return self::$_Name_Class;
    }
    /**
     * 返回执行对象方法名
     * @access protected
     * @return string
     */
    protected function get_function()
    {
        return self::$_Name_Function;
    }
    /**
     * 执行成功提示信息
     * @access protected
     * @param $message
     * @param $url
     * @param $time
     * @return null
    */
    protected function success($message='success',$url='#',$time=5)
    {
        $_setting = array("bgcolor"=>"floralwhite","color"=>"#000000","title"=>"Success");
        \Origin\Kernel\Parameter\Output::output($time, $message, $url, $_setting);
        return null;
    }
    /**
     * 错误提示
     * @access protected
     * @param $message
     * @param $url
     * @param $time
     * @return null
    */
    protected function error($message='error',$url='#',$time=5)
    {
        $_setting = array("bgcolor"=>"orangered","color"=>"floralwhite","title"=>"Error");
        \Origin\Kernel\Parameter\Output::output($time, $message, $url, $_setting);
        return null;
    }
    /**
     * @access public
     * @param array $array
     * @return null
    */
    protected function json($array=null)
    {
        \Origin\Kernel\Parameter\Output::json($array);
        return null;
    }
    /**
     * @access public
     * @param array $array
     * @return null
     */
    protected function xml($array=null)
    {
        \Origin\Kernel\Parameter\Output::xml($array);
        return null;
    }
    /**
     * @access public
     * @param string $head
     * @param string $body
     * @return null
    */
    protected function html($head=null,$body=null)
    {
        \Origin\Kernel\Parameter\Output::html($head,$body);
        return null;
    }
}