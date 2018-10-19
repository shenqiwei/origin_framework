<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/19
 * Time: 11:07
 */

namespace Origin\Kernel\Transaction\Example;

# 模板元素表述封装
use Origin\Model as Mapping;

class Pass
{
    /**
     * @access protected
     * @var string $_Error_code 错误编码
     */
    protected $_Error_code = null;
    /**
     * @access protected
     * @var array $_Access_array 准入信息数组
    */
    protected $_Access_array = null;
    /**
     * @access public
     * @param array $mapping 映射配置模板
     * @param string $key 抽取对象内容键名
     * @return mixed
     * @context 请求器封装
     */
    function index($mapping,$key){
        # 创建返回值变量
        $_receipt = null;
        # 创建请求方式
        $_method = "post";
        # 查看用户设置
        if(key_exists($_mark = Mapping::MODEL_METHOD_MARK,$mapping)){
            # 执行方法装载
            $_method = in_array(strtolower($mapping[$_method]),array('get','post'))?strtolower($mapping[$_method]):"post";
        }
        # 判断主要元素
        if(key_exists($_mark = Mapping::MODEL_MAPPING_MARK,$mapping)){
            $_column = $mapping[$_mark];
            if(is_array($_column) and !empty($_column)){
                # 设置锚点
                $_i = 0;
                foreach($_column as $_array){
                    if(key_exists($_key = Mapping::MODEL_MAPPING_COLUMN_NAME,$_array)){
                        if($_array[$_key] == $key){
                            $_value = input($_method.$key);
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
                                if(key_exists($_key = Mapping::MODEL_VALID_MARK,$mapping)){
                                    $_validates = $mapping[$_key];
                                    foreach($_validates as $_arr){
                                        # 验证长度
                                        if(key_exists($_min = Mapping::MODEL_VALID_COLUMN_MIN,$_arr) or key_exists($_max = Mapping::MODEL_VALID_COLUMN_MAX,$_arr)){
                                            # 创建错误状态变量
                                            # 判定最理想化状态
                                            if(isset($_min) and intval($_min) and isset($_max) and intval($_max)){
                                                if($_error = is_true('/^.*$/',$_value,intval($_min),intval($_max)) !== true){
                                                    $this->_Error_code = $_error;
                                                }
                                            }else{
                                                if(isset($_max) and intval($_max) > 0){
                                                    if($_error = is_true('/^.*$/',$_value,0,$_max) !== true){
                                                        $this->_Error_code = $_error;
                                                    }
                                                }elseif(intval($_min) > 0){
                                                    if($_error = is_true('/^.*$/',$_value,intval($_min),0) !== true){
                                                        $this->_Error_code = $_error;
                                                    }
                                                }
                                            }
                                            if(is_null($this->_Error_code)){
                                                if(key_exists($_key = Mapping::MODEL_VALID_NOT_NULL,$_arr) and is_bool($_arr[$_key]) and $_arr[$_key]){
                                                    if(is_null($_value) and (empty($_value) and !is_numeric($_value) and boolval($_value))){
                                                        $this->_Error_code = "value is null";
                                                    }
                                                }
                                            }
                                            if(is_null($this->_Error_code)){
                                                if(key_exists($_key = Mapping::MODEL_VALID_COLUMN_FORMAT_STRING,$_arr)){
                                                    if($_error = is_true($_arr[$_key],$_value) !== true){
                                                        $this->_Error_code = $_error;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $this->_Error_code = 'Not found validate model config';
                                }
                            }
                        }else{
                            $_i += 1;
                        }
                    }
                }
                if($_i === count($_column)){
                    $this->_Error_code = "Not found object column in pass model";
                }
            }
        }else{
            # 异常提示：未设置元素内容
            try{
                throw new \Exception('Origin Action Pass Error: Not found column array');
            }catch(\Exception $e){
                echo($e->getMessage());
                exit();
            }
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