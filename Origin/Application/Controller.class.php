<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Application.Controller *
 * version: 1.0 *
 * structure: common framework *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context: IoC 公共控制器
 */
namespace Origin;
/**
 * 功能控制器，负责内核功能方法的预加载调用
*/
class Controller
{
    /**
     * 获取当前操作类信息
     * @var string $_Name_Class
    */
    private $_Name_Class = null;
    /**
     * 获取当前执行方法信息
     * @var string $_Name_Function
    */
    private $_Name_Function = null;
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
        # 判断自动加载单元返回类型信息是否有效
        if(!is_null(\Origin\Kernel\Entrance::$_Class)){
            # 判断是否包含起始符号
            if(strpos(\Origin\Kernel\Entrance::$_Class,"\\") === 0){
                # 消除起始符号
                $this->_Name_Class = substr(\Origin\Kernel\Entrance::$_Class,1);
            }else{
                $this->_Name_Class = \Origin\Kernel\Entrance::$_Class;
            }
        }else{
            # 使用PHP类信息函数调用类信息
            $this->_Name_Class = get_class($this);
        }

    }
    /**
     * 创建欢迎页
     * @access protected
     * @return null
    */
    protected function welcome()
    {
        echo('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Origin架构开发版</title></head><body style="top:0px; left:0px;"><div style="margin: 0px auto; width:380px;"><h1 style="font-size:60px; color:black; margin: 0px;">Origin</h1><div style="font-size:10px; text-align: right;">Ver.0.1</div><br /><div style="padding-bottom:8px;">你好！欢迎使用Origin框架</div><div style="padding-bottom:8px;text-align: right;">Hello! Welcome to use Origin framework</div><div style="padding-bottom:8px;">こんにちは！使用を歓迎しOriginフレーム</div><div style="padding-bottom:8px;text-align: right;">안녕하세요.오신 것을 환영합니다 Origin 틀</div><div style="padding-bottom:8px;">Hallo!Willkommen in origin.</div><div style="padding-bottom:8px;text-align: right;">hej!velkommen til oprindelse ramme</div><div style="padding-bottom:8px;"> مرحبا!  مرحبا بكم في  الأصل  في إطار </div><div style="padding-bottom:8px;text-align: right;">Olá!BEM - vindo Ao Quadro de Origem</div><div style="padding-bottom:8px;">Xin chào!Chào mừng Origin sử dụng khung</div><div style="padding-bottom:8px;text-align: right;">szia!üdvözlöm Origin keret alkalmazása</div><br /><div style="text-align: center; ">\author\: <spac style="font-family: \'Arial Black\'; font-size:13px; text-decoration: underline;">ShenQiwei</spac> \time\:<spac style="font-family: \'Arial Black\'; font-size:13px; text-decoration: underline;">2017/02/03</spac></div></div></body></html>');
        exit();
    }
    /**
     * 单条信息显示方法
     * @access protected
     * @param $message
     * @return null;
    */
    protected function show($message)
    {
        echo($message);
        exit();
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
            $this->_Param_Array[$this->_Name_Class][$key] = $value;
        }else{
            # 异常提示：变量名称包含非合法符号
            try{
                throw new \Exception('Origin Apply Error: Variable name contains non legal symbols');
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
            str_replace(Config('APPLICATION_CONTROLLER'), '',
                str_replace(Config('DEFAULT_APPLICATION'), '',
                    str_replace(Config('ROOT_APPLICATION'), '',
                        str_replace('\\', '/', $this->_Name_Class)))));
        if(!is_null(\Origin\Kernel\Entrance::$_Function)){
            $_page = \Origin\Kernel\Entrance::$_Function;
        }
        if(!is_null($view ) and is_true($_regular, $view) === true){
            $_page = $view;
        }
        $_obj = new \Origin\Kernel\Graph\View($_dir, $_page);
        $_obj->view($this->_Param_Array[$this->_Name_Class]);
        return null;
    }
    /**
     * 返回执行对象类名
     * @access protected
     * @return string
     */
    protected function get_class()
    {
        return $this->_Name_Class;
    }
    /**
     * 返回执行对象方法名
     * @access protected
     * @return string
     */
    protected function get_function()
    {
        $this->_Name_Function = Config('DEFAULT_VIEW');
        if(is_array(debug_backtrace())){
            $_get_history = debug_backtrace();
            for($i=0; $i<count($_get_history); $i++){
                if($_get_history[$i]['class'] == $this->_Name_Class){
                    $this->_Name_Function = $_get_history[$i]['function'];
                    break;
                }else{
                    continue;
                }
            }
        }
        return $this->_Name_Function;
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
        $_file = str_replace('/',SLASH,ROOT.RING.'Template/Success.html');
        message($_file, $message, $url, $time);
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
        $_file = str_replace('/',SLASH,ROOT.RING.'Template/Error.html');
        message($_file, $message, $url, $time);
        return null;
    }
    /**
     * 执行失败提示
     * @access protected
     * @param $message
     * @param $url
     * @param $time
     * @return null
    */
    protected function failed($message='failed',$url='#',$time=5)
    {
        $_file = str_replace('/',SLASH,ROOT.RING.'Template/Failed.html');
        message($_file, $message, $url, $time);
        return null;
    }
}