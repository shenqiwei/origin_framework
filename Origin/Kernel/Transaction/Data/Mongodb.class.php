<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/18
 * Time: 11:12
 */
namespace Origin\Kernel\Transaction\Data;

class Mongodb
{
    /**
     * @access protected
     * @var object $_Dao_connect 数据对象
     */
    # 数据链接对象变量
    public $_Dao_connect = null;
    /**
     * @access protected
     * @var string $_Table 表对象
     */
    protected $_Table = null;
    /**
     * @access protected
     * @var array $_Column 字段配置
     */
    protected $_Column = null;
    /**
     * @access protected
     * @var array $_Data 数据
     */
    protected $_Data = null;
    /**
     * @access protected
     * @var string $_Type 语句执行类型
     */
    protected $_Type = "select";
    /**
     * @access protected
     * @var string $_Error_code 错误信息
     */
    public $_Error_code = null;
    /**
     * @access public
     * @param string $source 数据源指向
     */
    function __construct($source=null)
    {
        if(is_null($source)){
            # 比对数据源配置信息指向
            if(Config("DATA_TYPE") === "mongodb"){
                $this->_Dao_connect = Dao();
            }else{
                # 无效数据源
                $this->_Error_code = "Dao source is not mongodb";
            }
        }else{
            $this->_Dao_connect = Mongodb($source);
        }
    }
    /**
     * @access public
     * @param string $table
     * @context 表格
     */
    function setTable($table)
    {
        $this->_Table = $table;
    }
    /**
     * @access public
     * @param array $column
     * @context 字段
     */
    function setColumn($column)
    {
        $this->_Column = $column;
    }
    /**
     * @access public
     * @param array $data
     * @context 数据
     */
    function setData($data)
    {
        $this->_Data = $data;
    }
    /**
     * @access public
     * @param string $type
     * @context 获取执行类型
     */
    function setType($type)
    {
        $this->_Table = $type;
    }
    /**
     * @access public
     * @return mixed
     * @context 语句执行函数
     */
    function execute()
    {
        # 创建返回值变量
        $_receipt = null;
        # 选择数据执行状态
        switch(trim(strtolower($this->_Type))){
            case "insert":
                $_receipt = $this->insert($this->_Table,$this->_Data['data']);
                break;
            case "update":
                $_receipt = $this->update($this->_Table,$this->_Data['data'],$this->_Data['where']);
                break;
            case "delete":
                $_receipt = $this->delete($this->_Table,$this->_Data['where']);
                break;
            case "count":
                $_receipt = $this->count($this->_Table,$this->_Data['where']);
                break;
            default:
                $_receipt = $this->select($this->_Table,$this->_Data['where']);
                break;
        }
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $where 条件对象
     * @return mixed
     * @context 查询操作
     */
    protected function select($table,$where)
    {
        return $this->_Dao_connect->set($table)->where($where)->select();
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $data 对象数据
     * @return mixed
     * @context 插入对象数据
     */
    protected function insert($table,$data)
    {
        return $this->_Dao_connect->set($table)->data($data)->insert();
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $data 对象数据
     * @param string|array $where 条件对象
     * @return mixed
     * @context 更新对象数据
     */
    protected function update($table,$data,$where)
    {
        return $this->_Dao_connect->set($table)->data($data)->where($where)->insert();
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $where 条件对象
     * @return mixed
     * @context 删除对象数据
     */
    protected function delete($table,$where)
    {
        return $this->_Dao_connect->set($table)->where($where)->delete();
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $where 条件对象
     * @return mixed
     * @context 查询总数
     */
    protected function count($table,$where)
    {
        $_receipt = null;
        $_receipt = $this->_Dao_connect->set($table)->where($where)->select();
        if(is_array($_receipt)){
            $_receipt = count($_receipt);
        }else{
            $_receipt = 0;
        }
        return $_receipt;
    }

    function getError()
    {
        return $this->_Error_code;
    }
}