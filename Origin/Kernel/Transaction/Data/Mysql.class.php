<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/18
 * Time: 11:09
 */
namespace Origin\Kernel\Transaction\Data;

class Mysql
{
    /**
     * @access protected
     * @var object $_Dao_connect 数据对象
     */
    # 数据链接对象变量
    protected $_Dao_connect = null;
    /**
     * @access protected
     * @var string $_Table 表对象
    */
    protected $_Table = null;
    /**
     * @access protected
     * @var array $_Major 主键配置
    */
    protected $_Major = null;
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
    protected $_Error_code = null;
    /**
     * @access public
     * @param string $source 数据源指向
     */
    function __construct($source=null)
    {
        if(is_null($source)){
            # 比对数据源配置信息指向
            if(Config("DATA_TYPE") === "mysql"){
                $this->_Dao_connect = Dao();
            }else{
                # 无效数据源
                $this->_Error_code = "Dao source is not mysql";
            }
        }else{
            $this->_Dao_connect = Mysql($source);
        }
    }
    /**
     * @access public
     * @param string $table
     * @context 表格
    */
    function setTable($table){
        $this->_Table = $table;
    }
    /**
     * @access public
     * @param array $major
     * @context 主键
    */
    function setMajor($major)
    {
        $this->_Major = $major;
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
     * @param string $type
     * @context 获取执行类型
    */
    function setType($type)
    {
        $this->_Table = $type;
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
                # 初始化条件变量
                $_where = null;
                # 判断条件结构内容是否有效
                if(key_exists("where",$this->_Data)){
                    # 判断条件内容状态
                    if(!is_null($this->_Data['where']) and !empty($this->_Data['where'])){
                        $_where = $this->_Data['where'];
                    }else{
                        # 调用主键条件内容
                        if(!is_null($this->_Major)){
                            $_where = $this->_Major;
                        }else{
                            $this->_Error_code = "Not found [where] information";
                        }
                    }
                }else{
                    if(!is_null($this->_Major)){
                        $_where = $this->_Major;
                    }else{
                        $this->_Error_code = "Not found [where] information";
                    }
                }
                if(is_null($this->_Error_code))
                    $_receipt = $this->update($this->_Table,$this->_Data['data'],$_where);
                break;
            case "delete":
                # 初始化条件变量
                $_where = null;
                # 判断条件结构内容是否有效
                if(key_exists("where",$this->_Data)){
                    # 判断条件内容状态
                    if(!is_null($this->_Data['where']) and !empty($this->_Data['where'])){
                        $_where = $this->_Data['where'];
                    }else{
                        # 调用主键条件内容
                        if(!is_null($this->_Major)){
                            $_where = $this->_Major;
                        }else{
                            $this->_Error_code = "Not found [where] information";
                        }
                    }
                }else{
                    if(!is_null($this->_Major)){
                        $_where = $this->_Major;
                    }else{
                        $this->_Error_code = "Not found [where] information";
                    }
                }
                if(is_null($this->_Error_code))
                    $_receipt = $this->delete($this->_Table,$_where);
                break;
            case "count":
                # 初始化条件变量
                $_where = null;
                # 判断条件结构内容是否有效
                if(key_exists("where",$this->_Data)){
                    # 判断条件内容状态
                    if(!is_null($this->_Data['where']) and !empty($this->_Data['where'])){
                        $_where = $this->_Data['where'];
                    }else{
                        # 调用主键条件内容
                        if(!is_null($this->_Major)){
                            $_where = $this->_Major;
                        }
                    }
                }else{
                    if(!is_null($this->_Major)){
                        $_where = $this->_Major;
                    }
                }
                $_receipt = $this->count($this->_Table,$_where);
                break;
            default:
                # 初始化条件变量
                $_where = null;
                # 判断条件结构内容是否有效
                if(key_exists("where",$this->_Data)){
                    # 判断条件内容状态
                    if(!is_null($this->_Data['where']) and !empty($this->_Data['where'])){
                        $_where = $this->_Data['where'];
                    }else{
                        # 调用主键条件内容
                        if(!is_null($this->_Major)){
                            $_where = $this->_Major;
                        }
                    }
                }else{
                    if(!is_null($this->_Major)){
                        $_where = $this->_Major;
                    }
                }
                $_receipt = $this->select($this->_Table,$this->_Data['field'],$_where,$this->_Data['group'],$this->_Data['order'],$this->_Data['limit']);
                break;
        }
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string $field 字段
     * @param string|array $where 条件对象
     * @param string|array $order 排序对象
     * @param string|array $group 分组对象
     * @param string|array $limit 显示界限对象
     * @return mixed
     * @context 查询操作
     */
    protected function select($table,$field=null,$where=null,$group=null,$order=null,$limit=null)
    {
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "Not found table";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($field))
            $this->_Dao_connect->field($field);
        if(!is_null($where))
            $this->_Dao_connect->where($where);
        if(!is_null($group))
            $this->_Dao_connect->group($group);
        if(!is_null($order))
            $this->_Dao_connect->order($where);
        if(!is_null($limit))
            $this->_Dao_connect->limit($limit);
        if(is_null($this->_Error_code))
            return $this->_Dao_connect->select();
        else
            return null;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $data 对象数据
     * @return mixed
     * @context 插入对象数据
     */
    protected function insert($table,$data=null)
    {
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "Not found table";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($data))
            $this->_Dao_connect->data($data);
        else
            # 无效数据对象
            $this->_Dao_connect = "Not found where information";
        if(is_null($this->_Error_code))
            return $this->_Dao_connect->insert();
        else
            return null;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $data 对象数据
     * @param string|array $where 条件对象
     * @return mixed
     * @context 更新对象数据
     */
    protected function update($table,$data=null,$where=null)
    {
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "Not found table";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($data))
            $this->_Dao_connect->data($data);
        else
            # 无效数据对象
            $this->_Error_code = "";
        if(!is_null($where))
            $this->_Dao_connect->where($where);
        else
            # 无效条件对象
            $this->_Dao_connect = "Not found where information";
        if(is_null($this->_Error_code))
            return $this->_Dao_connect->update();
        else
            return null;
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
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "Not found table";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($where))
            $this->_Dao_connect->where($where);
        else
            # 无效条件对象
            $this->_Dao_connect = "Not found where information";
        if(is_null($this->_Error_code))
            return $this->_Dao_connect->delete();
        else
            return null;
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
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "Not found table";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($where))
            $this->_Dao_connect->where($where);
        if(is_null($this->_Error_code))
            return $this->_Dao_connect->count();
        else
            return null;
    }

    function getError()
    {
        return $this->_Error_code;
    }
}