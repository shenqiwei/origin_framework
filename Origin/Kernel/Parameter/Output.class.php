<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/6/25
 * Time: 11:48
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
     * @param array $array 数据
     * @context xml内容输出
     */
    static function xml($array=null)
    {
        if(is_array($array)){
            $_xml = "<xml>";
            foreach ($array as $_key => $_val) {
                if (is_numeric($_val)) {
                    $_xml .= "<" . $_key . ">" . $_val . "</" . $_key . ">";
                } else
                    $_xml .= "<" . $_key . "><![CDATA[" . $_val . "]]></" . $_key . ">";
            }
            $_xml .= "</xml>";
            $array = $_xml;
        }
        header("Content-Type:text/xml;charset=utf-8");
        echo($array);
    }
    /**
     * @access public
     * @param string $head
     * @param string $body 数据
     * @context html内容输出
     */
    static function html($head=null,$body=null)
    {
        if(!is_null($body)){
            $_html = "<!DOCTYPE html><html lang=\"en\">";
            if(!is_null($head)){
                $_html .= "<head><meta charset=\"UTF-8\">".$head."</head>";
            }else{
                $_html .= "<head><meta charset=\"UTF-8\"><title>Origin Page</title></head>";
            }
            $_html .= "<body>";
            $_html .= $_html;
            $_html .= "</body></html>";
        }else{
            $_html = "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"UTF-8\"><title>Origin Page</title></head><body>空白模板</body></html>";
        }
        header("Content-Type:text/html;charset=utf-8");
        echo($_html);
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
        include(str_replace('/',SLASH,ROOT.RING.'Template/Message.html'));
        exit();
    }
    /**
     * @access public
     * @param array $error_arr 异常信息数组
     * @return null
     * @context 底层异常显示模块
     */
    public function base($error_arr)
    {
        $_error_msg = $error_arr["message"];
        $_error_msg = explode("#",$_error_msg);
        include(str_replace('/',SLASH,ROOT.RING.'Template/Debug.html'));
        return null;
    }
}