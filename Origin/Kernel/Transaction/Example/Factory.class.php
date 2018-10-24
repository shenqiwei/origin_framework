<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/19
 * Time: 16:23
 */

namespace Origin\Kernel\Transaction\Example;

# 模板元素表述封装
use Origin\Model as Mapping;
# 调用内核验证模块
use Origin\Kernel\Parameter\Validate;

class Factory
{
    /**
     * @access protected
     * @var string $_Error_code 错误编码
     */
    protected $_Error_code = null;
    /**
     * @access protected
     * @var string $_Method 请求器类型
    */
    protected $_Method = "post";
    /**
     * @access public
     * @param string $method 请求类型
    */
    function setMethod($method)
    {
        $this->_Method = $method;
    }
    /**
     * @access public
     * @param array $model 元素模板/请求器模板
     * @param string $type 执行语句类型
     * @return mixed
     * @context 过程装载
    */
    function index($model,$type=null){
        # 创建返回值变量
        $_receipt = null;
        # 创建请求方式
        $_method = "post";
        # 查看用户设置
        if(key_exists($_mark = Mapping::MODEL_METHOD_MARK,$model)){
            # 执行方法装载
            $_method = in_array(strtolower($model[$_method]),array('get','post'))?strtolower($model[$_method]):"post";
            # 获取模板信息
        }else{
            $_method = $this->_Method;
        }
        # 创建数据
        $_data = array();
        if(key_exists($_mark = Mapping::MODEL_MAPPING_MARK,$model)){
            foreach($model[$_mark] as $_array){
                # 创建元素对象
                $_column = null;
                if(key_exists($_key = Mapping::MODEL_MAPPING_COLUMN_NAME,$_array)){
                    $_column = $_array[$_key];
                }
                $_value = Input($_method.".".$_column);
                if(key_exists($_key = Mapping::MODEL_MAPPING_COLUMN_IS_TO,$_array) and is_bool($_array[$_key]) and $_array[$_key]){
                    if(key_exists($_key = Mapping::MODEL_MAPPING_COLUMN_TYPE,$_array)){
                        switch($_array[$_key]){
                            case "string":
                                $_value = strval($_value);
                                break;
                            case "boolean":
                                $_value = boolval($_value);
                                break;
                            case "float":
                                $_value = floatval($_value);
                                break;
                            case "double":
                                $_value = doubleval($_value);
                                break;
                            default:
                                $_value = intval($_value);
                                break;
                        }
                    }
                }
                if(key_exists($_key = Mapping::MODEL_MAPPING_COLUMN_TO_VALID,$_array) and is_bool($_array[$_key]) and $_array[$_key]){
                    if(key_exists($_key = Mapping::MODEL_VALID_MARK,$model)){
                        $_validates = $model[$_key];
                        foreach($_validates as  $_mark => $_arr){
                            if($_mark != $_column)
                                continue;
                            # 验证长度
                            if(key_exists($_min = Mapping::MODEL_VALID_COLUMN_MIN,$_arr) or key_exists($_max = Mapping::MODEL_VALID_COLUMN_MAX,$_arr)){
                                # 创建错误状态变量
                                # 判定最理想化状态
                                if(isset($_min) and intval($_min) and isset($_max) and intval($_max)){
                                    if(($_error = is_true('/^.*$/',$_value,intval($_min),intval($_max))) !== true){
                                        $this->_Error_code = $_error;
                                    }
                                }else{
                                    if(isset($_max) and intval($_max) > 0){
                                        if(($_error = is_true('/^.*$/',$_value,0,$_max)) !== true){
                                            $this->_Error_code = $_error;
                                        }
                                    }elseif(intval($_min) > 0){
                                        if(($_error = is_true('/^.*$/',$_value,intval($_min),0)) !== true){
                                            $this->_Error_code = $_error;
                                        }
                                    }
                                }
                            }
                            if(is_null($this->_Error_code)){
                                if(key_exists($_key = Mapping::MODEL_VALID_NOT_NULL,$_arr) and is_bool($_arr[$_key]) and $_arr[$_key]){
                                    if(is_null($_value) or (empty($_value) and !is_numeric($_value) and !boolval($_value))){
                                        $this->_Error_code = "value is null";
                                    }
                                }
                            }
                            if(is_null($this->_Error_code)){
                                if(key_exists($_key = Mapping::MODEL_VALID_COLUMN_FORMAT_STRING,$_arr)){
                                    if(($_error = is_true($_arr[$_key],$_value)) !== true){
                                        $this->_Error_code = $_error;
                                    }
                                }
                            }
                        }
                    }else{
                        $this->_Error_code = 'Not found validate model config';
                    }
                }
                if(is_null($this->_Error_code)){
                    # 装入值
                    $_data[$_column] = $_value;
                }
            }
        }elseif(key_exists($_mark = Mapping::MAPPING_COLUMN_MARK,$model)){
            # 遍历字段列表
            foreach($model[$_mark] as $_array){
                # 创建元素对象
                $_column = null;
                if(key_exists($_key = Mapping::MAPPING_COLUMN_OPTION,$_array)){
                    $_column = $_array[$_key];
                }
                $_field = null;
                if(key_exists($_key = Mapping::MAPPING_FIELD_OPTION,$_array)){
                    $_field = $_array[$_key];
                    if(is_null($_column)) $_column = $_field;
                }else{
                    $this->_Error_code = "Not found field option";
                }
                if(is_null($this->_Error_code)){
                    # 创建默认值变量
                    $_default = null;
                    if(key_exists($_key = Mapping::MAPPING_DEFAULT_OPTION,$_array))
                        $_default = $_array[$_key];
                    $_value = Input($_method.".".$_column,$_default);
                    # 设置数据类型
                    $_type = "string";
                    if(key_exists($_key = Mapping::MAPPING_TYPE_OPTION,$_array)){
                        switch(strtolower($_array[$_key])){
                            case "string":
                                $_value = strval($_value);
                                break;
                            case "boolean":
                                $_value = boolval($_value);
                                break;
                            case "float":
                                $_value = floatval($_value);
                                break;
                            case "double":
                                $_value = doubleval($_value);
                                break;
                            default:
                                $_value = intval($_value);
                                break;
                        }
                    }
                    # 设置数据长度
                    $_size = 255;
                    if($_type == 'int' or $_type == 'double' or $_type == 'float') $_size = 11;
                    if(key_exists($_key = Mapping::MAPPING_SIZE_OPTION,$_array)){
                        $_size = intval($_array[$_key]);
                    }
                    # 设置空状态
                    $_not_null = true;
                    if(key_exists($_key = Mapping::MAPPING_IS_NULL_OPTION,$_array)){
                        $_not_null = boolval($_array[$_key]);
                    }
                    # 调用验证封装函数库
                    $_validate = new Validate($_value,null,0,$_size,$_not_null);
                    # 调用主验证函数
                    $_status_value = $_validate->main();
                    if($_status_value !== true){
                        # 执行特例
                        if(key_exists($_key = Mapping::MAPPING_QUERY_TYPE_OPTION,$_array)){
                            $_query_list = explode(",",$_array[$_key]);
                            if(in_array($type,$_query_list)){
                                # 接入错误信息
                                $this->_Error_code = $_status_value;
                                break;
                            }
                        }else{
                            # 接入错误信息
                            $this->_Error_code = $_status_value;
                            break;
                        }
                    }else{
                        # 装入值
                        $_data[$_field] = $_value;
                    }
                }
            }
        }else{
            $this->_Error_code = "Model is invalid";
        }
        if(is_null($this->_Error_code)){
            $_receipt = $_data;
        }
        return $_receipt;
    }
    /**
     * @access public
     * @return string
     * @context 返回错误信息内容
     */
    function getErrorMsg()
    {
        return $this->_Error_code;
    }
}