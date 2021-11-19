<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2017
 * @context: Origin在线请求器
 */
namespace Origin\Package;

use CURLFile;

class Curl
{
    /**
     * @access protected
     * @var array $CurlReceipt 请求返回信息
     */
    protected $CurlReceipt = array();

    /**
     * @access protected
     * @var boolean $CurUtf8 是否执行utf-8转码
     */
    protected $CurUtf8 = false;

    /**
     * 构造器，设置是否强制utf8编码
     * @access public
     * @param boolean $bool 设置强制utf-8编码转换
     * @return void
    */
    function __construct(bool $bool=false)
    {
        $this->CurUtf8 = $bool;
    }

    /**
     * get请求函数
     * @access public
     * @param string $url 访问地址
     * @param array $param 访问参数，可以使用get参数结构或者（k/v）数组结构
     * @param array $header 报文
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return array|bool|string|null 返回远程请求结果内容
     */
    function get(string $url, array $param=[], array $header=[], bool $ssl_peer=false, bool $ssl_host=false)
    {
        $curl = curl_init();
        if(!empty($header)) # 设置请求头
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, boolval($ssl_peer));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, boolval($ssl_host));
        if (!is_null($param))
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        $receipt = curl_exec($curl);
        if ($this->CurUtf8)
            # 将会输内容强制转化为utf-8
            $receipt = mb_convert_encoding($receipt, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        $this->CurlReceipt['errno'] = curl_errno($curl);
        $this->CurlReceipt['error'] = curl_error($curl);
        curl_close($curl);
        return $receipt;
    }

    /**
     * post请求函数
     * @access public
     * @param string $url 访问地址
     * @param array $param 访问参数，（k/v）数组结构
     * @param array $header 报文
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return array|bool|string|null 返回远程请求结果内容
     */
    function post(string $url, array $param=[], array $header=[], bool $ssl_peer=false, bool $ssl_host=false)
    {
        $curl = curl_init();
        if(!empty($header)) # 设置请求头
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl_peer);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $ssl_host);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        $receipt = curl_exec($curl);
        if ($this->CurUtf8)
            # 将会输内容强制转化为utf-8
            $receipt = mb_convert_encoding($receipt, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        $this->CurlReceipt['errno'] = curl_errno($curl);
        $this->CurlReceipt['error'] = curl_error($curl);
        curl_close($curl);
        return $receipt;
    }

    /**
     * 文件上传
     * @access public
     * @param string $url 访问地址
     * @param array $header 报文
     * @param string $folder 本地文件地址
     * @param string $type 文件类型
     * @param string $input 表单名
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return bool|string 返回远程请求结果内容
     */
    function upload(string $url, string $folder, string $type, array $header=[], string $input="pic", bool $ssl_peer=false, bool $ssl_host=false)
    {
        $curl = curl_init();
        if(!empty($header)){
            # 设置请求头
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            # 返回response头部信息
            curl_setopt($curl, CURLOPT_HEADER, false);
        }
        $file = new CURLFile($folder,$type);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS,array($input=>$file));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, boolval($ssl_peer));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, boolval($ssl_host));
        $receipt = curl_exec($curl);
        curl_close($curl);
        return $receipt;
    }

    /**
     * 获取请求后返回值内容
     * @access public
     * @return array 返回请求结果内容
    */
    function get_curl_receipt()
    {
        return $this->CurlReceipt;
    }
}