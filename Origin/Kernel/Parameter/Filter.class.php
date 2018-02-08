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
 * create Time: 2017/01/09 11:04
 * update Time: 2017/01/09 14:59
 * chinese Context: IoC 变量过滤封装类
 */

namespace Origin\Kernel\Parameter;
/**
 * 参数过滤基类，公共类
 */
class Filter
{
    /**
     * 全局变量，用于方法间值传递
     * 传递过滤对象
     * @access private
     * @var mixed $_Filter
    */
    private $_Filter = null;
    /**
     * 全局变量，用于方法间值传递
     * 传递值的限定类型
     * @access private
     * @var string $_type
    */
    private $_Type = null;
    /**
     * 全局变量，用于方法间值传递
     * 设定默认值，在验证失效后，系统会自动用默认值补充，前提是默认值合规
     * @access private
     * @var mixed $_Default
    */
    private $_Default = null;
    /**
     * 构造函数，对过滤对象及对应信息进行装在
     * @access public
     * @param mixed $filter
     * @param string $type
     * @param mixed $default
    */
    function __construct($filter, $type='string', $default=null)
    {
        $this->_Filter = trim($filter);
        $this->_Type = trim($type);
        $this->_Default = trim($default);
    }
    /**
     * 过滤结构主方法，用于返回验证后信息
     * 根据选择数据验证方式，调用对应的验证方法，然后返回验证后数据
     * @access public
     * @return mixed
    */
    function main()
    {
        switch($this->_Type){
            case 'int':
            case 'integer':
                $this->__int();
                break;
            case 'float':
                $this->__float();
                break;
            case 'double':
                $this->__double();
                break;
            case 'boolean':
                $this->__boolean();
                break;
            default:
                $this->__string();
                break;
        }
        return $this->_Filter;
    }
    /**
     * 对字符串型数据进行基础结构验证
     * 当输入的字符串中含有敏感单词或字母，会被转化结构进行转化，并进行关键符号转义
     * @access private
    */
    private function __string()
    {
        if(!empty($this->_Filter) || strlen(trim($this->_Filter))){
            $this->_Filter = htmlspecialchars($this->__sql(strval($this->_Filter)));
        }else{
            $this->_Filter = htmlspecialchars($this->__sql(strval($this->_Filter)));
        }
    }
    /**
     * 低强度防注入方法，用于对字符串进行低效验证，并将指定字符转化为html标签
     * @access private
     * @param string $param
     * @return string
    */
    private function __sql($param){
        /**
         * @var string _regular
         */
        $_regular = '(select|insert|update|delete|drop|create|alter|from|set|into|show|count|where|limit|group|order|left|join|and|exec)';
        if(preg_match($_regular, $param)){
            $param = str_replace(' ','&nbsp;',$param);
        }
        return $param;
    }
    /**
     * 对整数型数据进行基础验证和转化
     * 当输入值为字符串，则会对字符串进行基础验证：
     * 判断字符串是否有字符串组成，如果是进行类型转化，并判断装换后值状态。
     * 如果转换至为0，则将默认值赋入，如果未设置默认值，或者默认值不符合整数型要求，则等于0
     * @access private
    */
    private function __int()
    {
        if(!is_numeric($this->_Filter)){
            $this->_Filter = intval($this->_Filter);
            if($this->_Filter == 0){
                $this->_Filter = intval($this->_Default);
            }
        }else{
            $this->_Filter = intval($this->_Filter);
        }
    }
    /**
     * 对单精度浮点型数据进行基础验证和转化
     * 当输入值为字符串，则会对字符串进行基础验证：
     * 判断字符串是否有字符串组成，如果是进行类型转化，并判断装换后值状态。
     * 如果转换至为0，则将默认值赋入，如果未设置默认值，或者默认值不符合整数型要求，则等于0
     * @access private
     */
    private function __float()
    {
        if(!is_numeric($this->_Filter)){
            $this->_Filter = floatval($this->_Filter);
            if($this->_Filter == 0.0){
                $this->_Filter = floatval($this->_Default);
            }
        }else{
            $this->_Filter = intval($this->_Filter);
        }
    }
    /**
     * 对双精度浮点型数据进行基础验证和转化
     * 当输入值为字符串，则会对字符串进行基础验证：
     * 判断字符串是否有字符串组成，如果是进行类型转化，并判断装换后值状态。
     * 如果转换至为0，则将默认值赋入，如果未设置默认值，或者默认值不符合整数型要求，则等于0
     * @access private
     */
    private function __double()
    {
        if(!is_numeric($this->_Filter)){
            $this->_Filter = doubleval($this->_Filter);
            if($this->_Filter == 0.0){
                $this->_Filter = doubleval($this->_Default);
            }
        }else{
            $this->_Filter = doubleval($this->_Filter);
        }
    }
    /**
     * 对布尔型数据进行基础验证和转化
     * 当输入的是字符串或整数型，则会对字符串进行基础验证：
     * 如果字符串部值为空时，数据值等于 true，反之等于false
     * 如果整数型值大于0时，数据值等于 true，反之等于false
     * @access private
    */
    private function __boolean()
    {
        if(is_string($this->_Filter)){
            if(!empty(trim($this->_Filter))){
                $this->_Filter = true;
            }else{
                $this->_Filter = false;
            }
        }elseif(is_numeric($this->_Filter)){
            if(intval($this->_Filter) > 0){
                $this->_Filter = true;
            }else{
                $this->_Filter = false;
            }
        }else{
            $this->_Filter = boolval($this->_Filter);
        }
    }
}