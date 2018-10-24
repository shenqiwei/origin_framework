<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/18
 * Time: 11:10
 */
namespace Origin\Kernel\Transaction\Data;

use Origin\Kernel\Transaction\Example\Factory;

class Redis
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
     * @var int $_Cycle 数据节点周期
    */
    protected $_Cycle = 0;
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
            if(Config("DATA_TYPE") === "redis"){
                $this->_Dao_connect = Dao();
            }else{
                # 无效数据源
                $this->_Error_code = "Dao source is not redis";
            }
        }else{
            $this->_Dao_connect = Redis($source);
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
     * @param int $cycle 周期参数
     * @context 设置周期信息
    */
    function setCycle($cycle)
    {
        $this->_Cycle = $cycle;
    }
    /**
     * @access public
     * @param string $type 执行类型
     * @return mixed
     * @context 数据操作执行函数
    */
    function execute()
    {
        # 创建返回值变量
        $_receipt = null;
        # 选择数据执行状态
        switch(trim(strtolower($this->_Type))){
            case "insert":
                $_receipt = $this->insert($this->_Table,$this->_Data['data'],$this->_Cycle);
                break;
            case "update":
                $_receipt = $this->update($this->_Table,$this->_Data['data'],$this->_Cycle);
                break;
            case "delete":
                $_receipt = $this->delete($this->_Table,$this->_Data['field']);
                break;
            case "count":
                $_receipt = $this->count($this->_Table);
                break;
            default:
                $_receipt = $this->select($this->_Table,$this->_Data['field']);
                break;
        }
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $field 参数对象
     * @param string $column 元素对象
     * @return mixed
     * @context 查询操作
     */
    protected function select($field,$column=null)
    {
        if(is_null($column)){
            return $this->_Dao_connect->hash()->listing($field);
        }else{
            return $this->_Dao_connect->hash()->getlist($field,$column);
        }
    }
    /**
     * @access protected
     * @param string $field 参数对象
     * @param array $data 值对象列表
     * @param int $cycle 周期
     * @return mixed
     * @context 查询操作
     */
    protected function insert($field,$data,$cycle)
    {
        $_i = 0;
        if(is_array($data) and !empty($data)){
            foreach($data as $_field => $_value){
                if(!is_null($this->_Dao_connect->hash()->create($field,$_field,$_value)))
                    $_i++;
            }
            if($cycle){
                if(empty(intval($cycle)))
                    $this->_Dao_connect->key()->setSec($field,intval($cycle));
            }

        }
        return $_i;
    }
    /**
     * @access protected
     * @param string $field 参数对象
     * @param array $data 值对象列表
     * @param int $cycle 周期
     * @return mixed
     * @context 查询操作
     */
    protected function update($field,$data,$cycle)
    {
        $_i = 0;
        if(is_array($data) and !empty($data)){
            foreach($data as $_field => $_value){
                if(!is_null($this->_Dao_connect->hash()->reCreate($field,$_field,$_value)))
                    $_i++;
            }
            if($cycle){
                if(empty(intval($cycle)))
                    $this->_Dao_connect->key()->setSec($field,intval($cycle));
            }
        }
        return $_i;
    }
    /**
     * @access protected
     * @param string $field 参数对象
     * @param array $column 元素对象
     * @return mixed
     * @context 查询操作
     */
    protected function delete($field,$column)
    {
        $_i = 0;
        if(is_array($column) and !empty($column)){
            foreach($column as $_field){
                if(!is_null($this->_Dao_connect->hash()->reCreate($field,$_field)))
                    $_i++;
            }
        }
        return $_i;
    }
    /**
     * @access protected
     * @param string $field 参数对象
     * @return mixed
     * @context 查询操作
     */
    protected function count($field)
    {
        return $this->_Dao_connect->hash()->len($field);
    }

    function getError()
    {
        return $this->_Error_code;
    }
}