<?php
/**
 * coding: utf-8 *
 * system OS: windows10 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: Zeroes.Package.Listen.Action *
 * version: 1.0 *
 * structure: common framework *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/11/28
 * update Time: 2017/11/28 11:16
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 * @deprecated Zero 模板执行单元
 */
namespace Package\Listen;
# 调用文件控制函数
use Library\Config as Config;
use Library\Files as Files;
use Package\Param\Filter as Filter;
use Package\Query\Module\Mysql as Mysql;

# 调用配置控制函数
# 调用监听类包
# 调用mysql操作类包
# 模板控制单元类
class Model
{
    /**
     * @access protected
     * @var array $_model
     * @contact 模板结构映射信息
     */
    protected $_model = null;
    /**
     * @access protected
     * @var string $_method
     * @contact 数据结构类型
     */
    protected $_method = 'key_val';
    /**
     * @access protected
     * @var string $_query ;
     * @contact 预设执行语句
     */
    protected $_query = null;
    /**
     * @access protected
     * @var array $_value ;
     * @contact 请求值数组
     */
    protected $_value = null;
    /**
     * @access protected
     * @var object $_object
     * @contact 实例化对象
     */
    protected $_object = null;
    /**
     * @access protected
     * @var string $_error_number
     * @contact 错误编号
     */
    protected $_error_number = '00000';
    /**
     * @access protected
     * @var string $_error_msg
     * @contact 错误信息
     */
    protected $_error_msg = null;
    /**
     * @access public
     * @param object $object 回调对象
     * @contact 构造函数，用于对象结构装载
     */
    function __construct($object = null)
    {
        # 实例化本身，并装入内建变量中
        if (!is_null($object)) {
            $this->set_object(new $object());
        }
    }
    /**
     * @access protected
     * @return mixed
     */
    static function instance()
    {
        # 执行内对象声明
        $_receipt = new self(__CLASS__);
        # 对象信息回写
        $_receipt->set_object($_receipt);
        # 回调内对象
        return $_receipt->get_object();
    }

    /**
     * @access private
     * @var object $object
     */
    protected function set_object($object)
    {
        $this->_object = $object;
    }
    /**
     * @access public
     * @return object|null
     * @contact 对象信息回调函数
     */
    function get_object()
    {
        return $this->_object;
    }
    /**
     * @access public
     * @param string $set_object
     * @return object|null
     * @contact 模板调用对象
     */
    function set($set_object = null)
    {
        # 设置对象地址目录
        $_catalog = \Package\Origin::$_object_root . DS . Config::instance()->config('MODEL_ROOT');
        # 设置对象文件信息
        $_file = is_null($set_object) ? \Package\Origin::$_object_class . SX : $set_object . SX;
        # 判断映射文件有效性
        if (Files::instance()->resource($_file, $_catalog)) {
            $this->_model = include($_catalog . DS . $_file);
        } else {
            $this->_error_number = '';
            $this->_error_msg = '';
        }
        return $this->_object;
    }
    /**
     * @access public
     * @param string|int $method
     * @return object
     * @content 参数类型
     */
    function prepare($method = 'key_val')
    {
        switch (strtolower($method)) {
            case 'list':
            case '0':
            case intval(0):
                $this->_method = 'list';
                break;
            default:
                $this->_method = 'key_val';

        }
        return $this->_object;
    }
    /**
     * @access public
     * @param string $model_object
     * @return object
     * @contact 应用模板调用函数
     */
    function model($model_object = null)
    {
        # 初始化模板变量
        $_model = null;
        # 初始化模板对象名称标签
        $_model_name = Config::instance()->config('Variable:MODEL_OBJECT_NAME_MARK');
        # 设置配置对象变量
        $_object = is_null($model_object) ? \Package\Origin::$_object_function : $model_object;
        if (is_array($this->_model)) {
            for ($_i = 0; $_i < count($this->_model); $_i++) {
                if (key_exists($_model_name, $this->_model[$_i]) and $_object === $this->_model[$_i][$_model_name]) {
                    $_model = $this->_model[$_i];
                    break;
                } else {
                    continue;
                }
            }
        } else {
            $this->_error_number = '';
            $this->_error_msg = '';
        }
        if (!is_null($_model)) {
            # 初始化请求类型变量
            $_request = 'post';
            # 初始化请求类型标签
            $_re_method = Config::instance()->config('Variable:MODEL_SERVICE_OBJECT_REQUEST_METHOD_MARK');
            # 初始化语句标签
            $_re_query = Config::instance()->config('Variable:MODEL_SERVICE_OBJECT_REQUEST_METHOD_MARK');
            # 初始化变量结构标签
            $_re_var = Config::instance()->config('Variable:MODEL_SERVICE_OBJECT_REQUEST_METHOD_MARK');
            if (key_exists($_re_method, $_model)) {
                $_request = $_model[$_re_method];
            }
            if (key_exists($_re_query, $_model)) {
                $this->_query = $_model[$_re_query];
            } else {
                $this->_error_number = '';
                $this->_error_msg = '';
            }
            if (key_exists($_re_var, $_model) and $this->_error_number === '00000') {
                $_filter = Filter::instance()->filter($_model[$_re_var], $_request);
                if ($_filter->error_number() === '00000') {
                    $this->_value = $_variable = $_filter->get_param_array();
                    if (strtolower($this->_method) === 'list') {
                        $this->_value = array_values($_variable);
                    }
                } else {
                    $this->_error_number = $_filter->error_number();
                    $this->_error_msg = $_filter->error_msg();
                }

            }
        } else {
            $this->_error_number = '';
            $this->_error_msg = '';
        }
        return $this->_object;
    }
    /**
     * 执行数据操作内容
     */
    function execute()
    {
        if ($this->_error_number === '00000') {
            return Mysql::instance()->query($this->_query)->perpare($this->_value)->execute();
        } else {
            return false;
        }
    }
    /**
     * @access public
     * @return string
     * @content 返回错误信息编号
     */
    function error_number()
    {
        return $this->_error_number;
    }
    /**
     * @access public
     * @return string
     * @content 返回错误信息内容
     */
    function error_msg()
    {
        return $this->_error_msg;
    }
}