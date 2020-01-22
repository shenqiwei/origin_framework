<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin模板管理
 */
namespace Origin\Kernel\Parameter;
# 文件内容输出
class Output
{
    /**
     * @access public
     * @param array $array 数据
     * @context Json字符串界面内容输出
    */
    static function json($array=null)
    {
        if(is_array($array)) $array = json_encode($array);
        header("Content-Type:application/json;charset=utf-8");
        echo($array);
    }
    /**
     * @access public
     * @param int $time 倒计时时间
     * @param string $message 提示信息
     * @param string $url 跳转地址
     * @param array $setting 模板显示内容设置
     * @context 输出预设模板内容
    */
    static function output($time=5,$message=null,$url="#",$setting=null)
    {
        $_time = htmlspecialchars(trim($time));
        $_message =  htmlspecialchars(trim($message));
        $_url = htmlspecialchars(trim($url));
        $_setting = $setting;
        if(strtolower($setting["title"]) == "success"){
            $_model = str_replace("/",DS,ROOT.Config("ROOT_RESOURCE")."/Public/Temp/200.html");
        }elseif(strtolower($setting["title"]) == "error"){
            $_model = str_replace("/",DS,ROOT.Config("ROOT_RESOURCE")."/Public/Temp/400.html");
        }
        if(isset($_model) and is_file($_model))
            include($_model);
        else
            include(str_replace('/',DS,ROOT.RING.'Template/message.html'));
        exit(0);
    }
    /**
     * @access public
     * @param array $error_arr 异常信息数组
     * @context 底层异常显示模块
     */
    public function base($error_arr)
    {
        $_error_msg = $error_arr["message"];
        $_error_msg = explode("#",$_error_msg);
        $_error_zero = explode(" in ",$_error_msg[0]);
        $_error_zero[1] = explode(":",str_replace(ROOT,null,$_error_zero[1]));
        $_error_zero_line = intval($_error_zero[1][1]);
        array_push($_error_zero,"Line : ". $_error_zero_line);
        array_push($_error_zero, trim(str_replace($_error_zero_line,null,$_error_zero[1][1]))." :");
        $_error_zero[1] = $_error_zero[1][0];
        $_error_zero[1] = "In : ".$_error_zero[1];
        array_splice($_error_msg,0,1,$_error_zero);
        include(str_replace('/',DS,ROOT.RING.'Template/debug.html'));
        exit(0);
    }
    /**
     * @access public
     * @param string $error_title 异常标题
     * @param string|array $error_msg 异常信息数组
     * @param array $error_file 异常文件描述数组
     * @context 应用异常显示模块
     */
    function exception($error_title,$error_msg,$error_file)
    {
        $_error_msg = array(
            "msg" => "{$error_title} [Error Code:0000-0] {$error_msg}",
            "file" => "{$error_file[0]["file"]}",
            "line" => "{$error_file[0]["line"]}",
            "function" => "{$error_file[0]["function"]}",
            "class" => "{$error_file[0]["class"]}"
        );
        if(is_array($error_msg))
            $_error_msg["msg"] = "{$error_title} [Error Code:{$error_msg[0]}] {$error_msg[2]}";
        include(str_replace('/',DS,ROOT.RING.'Template/error.html'));
        eLog($_error_msg["msg"]);
        eLog("in:{$_error_msg["file"]}");
        eLog("line:{$_error_msg["line"]}");
        exit(0);
    }
}