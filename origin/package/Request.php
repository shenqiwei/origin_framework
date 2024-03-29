<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin请求结构封装
 */
namespace Origin\Package;
/**
 * 参数请求基类，公共类
 */
class Request
{
    /**
     * @access private
     * @var string $Method 全局变量，用于方法间值的传递，请求器，请求类型
     */
    private $Method = 'request';

    /**
     * @access private
     * @var string $ValidateName 全局变量，用于方法间值的传递，请求器，需调用的参数对象
     */
    private $ValidateName;

    /**
     * @access private
     * @var mixed $Default 全局变量，用于方法间值的传递，请求其，当请求内容为空或不存在时，系统会放回默认值信息
    */
    private $Default;

    /**
     * 构造函数，用于对请求状态和请求获得值的过滤形式进行加载和判断，对象参数
     * @access public
     * @param string $validate_name 请求对象名称
     * @param mixed $default 默认值
     * @param string $method 请求方法
     * @return void
    */
    function __construct($validate_name, $default=null, $method='request')
    {
        # 请求方式正则，涵盖基本的5中表单请求，正则变量,method 用于验证请求方式，type用于转化数据类型
        $method_regular = '/^(request|post|get|delete|put){1}$/';
        if(is_true($method_regular, $method)){
            $this->Method = $method;
        }
        $this->ValidateName = $validate_name;
        $this->Default = $default;
    }

    /**
     * 请求器主方法，通过对请求方式的判断，获取请求对象的值信息
     * 根据查询变量的名称，进行数组判断及数组变量，返回相应值
     * 当查询值不存在或者为空时，系统将默认值赋入，当默认值为空或者null时，直接返回null值
     * @access public
     * @return string|null 返回请求对象值
    */
    public function main()
    {
        # 创建返回变量，设置值为 null
        $receipt = null;
        # 判断请求类型，并将请求对象中的值存入数组对象变量
        switch(strtolower($this->Method)){
            case 'get': $array = $_GET; break;
            case 'post': $array = $_POST; break;
            default: $array = $_REQUEST; break;
        }
        # 判断数组是否有有效
        if($array){# 判断php是否在捕捉请求时，对值信息进行过滤，如果没有则进行过滤
            # 判断数组中是否存在，需查询变量名,如果存在进行数组遍历，反之执行默认值装载
            if(array_key_exists($this->ValidateName, $array)){
                # 数组遍历，通过逐一比对获取查询变量信息值`
                foreach($array as $k => $v){
                    if($k == $this->ValidateName){
                        if(!is_array($v))
                            $receipt = addslashes($v);
                        else
                            $receipt = $v;
                        break;
                    }
                }
            }
        }
        if((empty($receipt) and $receipt != 0 and $receipt != '0' )or is_null($receipt)){
            # 当数组无效时，装载默认值
            if(!is_null($this->Default))
                $receipt = addslashes($this->Default);
        }
        return $receipt;
    }

    /**
     * 请求器删除方法，用于删除指定元素
     * @access public
     * @return void
    */
    function delete(){
        # 判断请求类型，并将请求对象中的值存入数组对象变量
        switch(strtolower($this->Method)){
            case 'get':
                if(array_key_exists($this->ValidateName, $_GET))
                    unset($_GET[$this->ValidateName]);
                break;
            case 'post':
                if(array_key_exists($this->ValidateName, $_POST))
                    unset($_POST[$this->ValidateName]);
                break;
            default:
                if(array_key_exists($this->ValidateName, $_REQUEST))
                    unset($_REQUEST[$this->ValidateName]);
                break;
        }
    }
}