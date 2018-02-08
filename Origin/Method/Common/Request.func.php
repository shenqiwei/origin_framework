<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Function.Method.Request *
 * version: 0.1*
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context:
 * IoC 请求器函数包
 */
/**
 * 请求器函数，通过调用内核控制器，实现请求结构功能，现有版本不支持put，delete请求支持
 * @access public
 * @param string $key
 * @param string $default
 * @param string $operate
 * @return mixed
 */
function Request($key, $default=null, $operate='rw')
{
    /**
     * @var string $_receipt
     * @var string $_regular
     * @var string $_type
     * @var string $_request
     * @var string $_object
     */
    # 创建返回值变量
    $_receipt = null;
    # 创建请求范围变量
    $_regular = '/^((post|get|request|put|delete)\.)?[\w]+(\-[\w]+)*$/';
    # 创建请求类型变量
    $_method = 'request';
    # 判断请求参数是否合规
    if($key and is_true($_regular, trim(strtolower($key))) === true){
        # 将请求参数根据要求转为数组
        if(strpos($key, '.')){
            $_request = explode('.', trim($key));
        }else{
            $_request = $key;
        }
        # 判断默认值信息状态,并根据特例进行转换
        if(!$default){
            if(is_int($default)){
                $default = intval($default);
            }elseif(is_float($default) or is_double($default)){
                $default = doubleval($default);
            }elseif(is_bool($default)){
                $default = boolval($default);
            }elseif(is_numeric($default)){
                $default = strval($default);
            }
        }
        # 判断请求信息是否为数组，如果是进行数组信息重载
        if(is_array($_request)){
            $_method = strtolower($_request[0]);
            $_request = $_request[1];
        }
        # 声明请求控制器对象
        $_obj= new \Origin\Kernel\Parameter\Request($_request,$default,$_method);
        if($operate == 'delete'){
            # 执行删除
            $_obj->delete();
        }else{
            # 执行请求验证，并接受返回值
            $_receipt =$_obj->main();
        }
    }
    return $_receipt;
}