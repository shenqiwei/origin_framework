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
     * Json字符串界面内容输出
     * @access public
     * @param array $array 数据
     * @return void
    */
    static function json($array)
    {
        if(is_array($array)) $array = json_encode($array);
        header("Content-Type:application/json;charset=utf-8");
        echo($array);
    }

    /**
     * 输出预设模板内容
     * @access public
     * @param int $time 倒计时时间
     * @param string|null $message 提示信息
     * @param string $url 跳转地址
     * @param array $setting 模板显示内容设置
     * @return void
    */
    static function output($time=5,$message=null,$url="#",$setting=array())
    {
        $_time = htmlspecialchars(trim($time));
        $_message =  htmlspecialchars(trim($message));
        $_url = htmlspecialchars(trim($url));
        $_setting = $setting;
        if(strtolower($setting["title"]) == "success"){
            $_model = replace(ROOT_RESOURCE."/public/template/200.html");
        }elseif(strtolower($setting["title"]) == "error"){
            $_model = replace(ROOT_RESOURCE."/public/template/400.html");
        }
        if(!isset($_model) or !is_file($_model))
            $_model = replace(ORIGIN.'template/201.html');
        include("{$_model}");
        if($_time) unset($_time);
        if($_message) unset($_message);
        if($_url) unset($_url);
        if($_setting) unset($_setting);
        exit(0);
    }
}