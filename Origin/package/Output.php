<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin模板管理
 */
namespace Origin\Package;
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
            $_model = str_replace("/",DS,ROOT.config("ROOT_RESOURCE")."/public/temp/200.html");
        }elseif(strtolower($setting["title"]) == "error"){
            $_model = str_replace("/",DS,ROOT.config("ROOT_RESOURCE")."/public/temp/400.html");
        }
        if(isset($_model) and is_file($_model))
            include($_model);
        else
            include(str_replace('/',DS,ROOT.RING.'template/message.html'));
        exit(0);
    }
}