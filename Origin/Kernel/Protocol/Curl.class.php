<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Parameter.Validate *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2018/02/10 09:30
 * update Time: 2018/02/12 14:30
 * chinese Context: 在线请求器
 */
namespace Origin\Kernel\Protocol;

class Curl
{
    /**
     * @access private
     * @var array $_curl_receipt
     * @contact 请求返回信息
    */
    private $_curl_receipt = array();
    # 构造方法
    function __construct()
    {}
    /**
     * @access public
     * @param string $url 访问地址
     * @param string/array 访问参数，可以使用get参数结构或者（k/v）数组结构
     * @return mixed
     * @content get请求函数
    */
    function get($url=null,$param=null)
    {
        $_receipt = null;
        if(!is_null($url)){
            $_curl = curl_init();
            if(!is_null($param)){
                if(is_array($param)){
                    $_param = null;
                    foreach($param as $_key => $_value){
                        $_param .= $_key.'='.$_value;
                    }
                    $url .= '?'.$_param;
                }else{
                    if(strpos($param,'?') === 0 && strpos($param,'=') >= 2){
                        $url .= $param;
                    }else{
                        if(strpos($param,'?') === false && strpos($param,'=') >= 2){
                            $url .= '?'.$param;
                        }
                    }
                }
            }
            curl_setopt($_curl,CURLOPT_URL,$url);
            curl_setopt($_curl,CURLOPT_RETURNTRANSFER,1);
            $_receipt = curl_exec($_curl);
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
     * @return mixed
     * @content get请求函数
     */
    function post($url,$param,$type='from')
    {
        $_receipt = null;
        if(!is_null($url)){
            $_curl = curl_init();
            curl_setopt($_curl,CURLOPT_URL,$url);
            curl_setopt($_curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($_curl,CURLOPT_HEADER,false);
            curl_setopt($_curl,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
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
            curl_setopt($_curl,CURLOPT_SAFE_UPLOAD,false);
            curl_setopt($_curl,CURLOPT_TIMEOUT,30);
            curl_setopt($_curl,CURLOPT_POSTFIELDS,$param);
            $_receipt = curl_exec($_curl);
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