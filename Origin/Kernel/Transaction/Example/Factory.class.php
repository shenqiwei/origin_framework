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
     * @var string $_Default 默认准入指向模板
     */
    protected $_Default = null;
    /**
     * @access public
     * @param string $default 默认指向
     * @context 设置默认准入模板
     */
    public function setDefault($default)
    {
        $this->_Default = $default;
    }
    /**
     * @access public
     * @param array $model 元素模板
     * @param array $mapping 请求器模板
     * @param string $type 执行语句类型
     * @return mixed
     * @context 过程装载
    */
    function index($model,$mapping,$type){
        # 创建返回值变量
        $_receipt = null;
        # 创建请求方式
        $_method = "post";
        # 查看用户设置
        if(key_exists($_mark = Mapping::MODEL_METHOD_MARK,$mapping)){
            # 执行方法装载
            $_method = in_array(strtolower($mapping[$_method]),array('get','post'))?strtolower($mapping[$_method]):"post";
        }
        # 创建数据
        $_data = array();
        # 遍历字段列表
        foreach($model as $_array){
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
                    $_type = strtolower($_array[$_key]);
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
                    array_push($_data,array($_field,$_value));
                }
            }
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