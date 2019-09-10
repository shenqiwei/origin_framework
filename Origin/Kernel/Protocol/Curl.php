<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Parameter.Validate *
 * version: 1.0 *
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 * @context: 在线请求器
 */
namespace Origin\Kernel\Protocol;

class Curl
{
    /**
     * @access protected
     * @var array $_curl_receipt
     * @contact 请求返回信息
     */
    protected $_curl_receipt = array();
    /**
     * @access protected
     * @var boolean $_curl_utf_8
     * @context 是否执行utf-8转码
     */
    protected $_curl_utf_8 = false;
    # 构造方法
    function __construct($bool=false)
    {
        $this->_curl_utf_8 = boolval($bool);
    }
    /**
     * @access public
     * @param string $url 访问地址
     * @param string/array 访问参数，可以使用get参数结构或者（k/v）数组结构
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return mixed
     * @content get请求函数
     */
    function get($url=null,$param=null,$ssl_peer=false,$ssl_host=false)
    {
        $_receipt = null;
        if(!is_null($url)){
            $_curl = curl_init();
            curl_setopt($_curl,CURLOPT_URL,$url);
            curl_setopt($_curl,CURLOPT_POST,false);
            curl_setopt($_curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($_curl,CURLOPT_SSL_VERIFYPEER,boolval($ssl_peer));
            curl_setopt($_curl,CURLOPT_SSL_VERIFYHOST,boolval($ssl_host));
            if(!is_null($param))
                curl_setopt($_curl,CURLOPT_POSTFIELDS,$param);
            $_receipt = curl_exec($_curl);
            if($this->_curl_utf_8)
                # 将会输内容强制转化为utf-8
                $_receipt = mb_convert_encoding($_receipt, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
            $this->_curl_receipt['errno'] = curl_errno($_curl);
            $this->_curl_receipt['error'] = curl_error($_curl);
            curl_close($_curl);
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $url 访问地址
     * @param string/array 访问参数，（k/v）数组结构
     * @param string/int $type 请求值类型 0：from 表单请求，1：json json字符串请求，2：xml xml文本标记请求
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return mixed
     * @content get请求函数
     */
    function post($url,$param,$type='from',$ssl_peer=false,$ssl_host=false)
    {
        $_receipt = null;
        if(!is_null($url)){
            $_curl = curl_init();
            curl_setopt($_curl,CURLOPT_URL,$url);
            curl_setopt($_curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($_curl,CURLOPT_HEADER,false);
            curl_setopt($_curl,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
            curl_setopt($_curl,CURLOPT_SSL_VERIFYPEER,boolval($ssl_peer));
            curl_setopt($_curl,CURLOPT_SSL_VERIFYHOST,boolval($ssl_host));
            curl_setopt($_curl,CURLOPT_POST,true);
            if($type !== 'from' or (is_numeric($type) and $type !== 0)){
                if($type === 'json' or (is_numeric($type) and $type === 1)){
                    if(is_array($param)){
                        $param = json_encode($param);
                    }
                    curl_setopt($_curl,CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type:application/json;charset=utf-8',
                            'Content-Length:'.strlen($param)
                        )
                    );
                }elseif($type === 'xml' or (is_numeric($type) and $type === 2)){
                    curl_setopt($_curl,CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type:text/xml;charset=utf-8'
                        )
                    );
                }
            }
            curl_setopt($_curl,CURLOPT_TIMEOUT,30);
            curl_setopt($_curl,CURLOPT_POSTFIELDS,$param);
            $_receipt = curl_exec($_curl);
            if($this->_curl_utf_8)
                # 将会输内容强制转化为utf-8
                $_receipt = mb_convert_encoding($_receipt, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
            $this->_curl_receipt['errno'] = curl_errno($_curl);
            $this->_curl_receipt['error'] = curl_error($_curl);
            curl_close($_curl);
        }
        return $_receipt;
    }

    function get_curl_receipt()
    {
        return $this->_curl_receipt;
    }
}