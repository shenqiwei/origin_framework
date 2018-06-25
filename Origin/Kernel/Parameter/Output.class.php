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
     * @param string $model html模板内容
     * @param int $time 倒计时时间
     * @param string $message 提示信息
     * @param string $url 跳转地址
     * @context 输出预设模板内容
    */
    static function output($model=null,$time=5,$message=null,$url="#")
    {
        $_temp = file_get_contents($model);
        $_temp = str_replace('{$time}', htmlspecialchars(trim($time)), $_temp);
        if (is_array($message)) $message = 'this is a default message';
        $_temp = str_replace('{$message}', htmlspecialchars(trim($message)), $_temp);
        $_temp = str_replace('{$url}', htmlspecialchars(trim($url)), $_temp);
        header("Content-Type:text/html;charset=utf-8");
        echo($_temp);
        exit();
    }
}