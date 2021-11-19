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
    static function json(array $array)
    {
        $array = json_encode($array);
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
    static function output(int $time=5, string $message=null, string $url="#", array $setting=[])
    {
        $time = htmlspecialchars(trim($time));
        $message =  htmlspecialchars(trim($message));
        $url = htmlspecialchars(trim($url));
        if(strtolower($setting["title"]) == "success")
            $model = replace(ROOT_RESOURCE."/public/template/200.html");
        elseif(strtolower($setting["title"]) == "error")
            $model = replace(ROOT_RESOURCE."/public/template/400.html");
        if(!isset($model) or !is_file($model))
            $model = replace(ORIGIN.'template/201.html');
        include("$model");
        if($time) unset($time);
        if($message) unset($message);
        if($url) unset($url);
        if($setting) unset($setting);
        exit(0);
    }
}