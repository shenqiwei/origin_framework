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
                $this->_Error_code = "";
            }
        }else{
            $this->_Dao_connect = Mysql($source);
        }
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
    public function select($table,$field=null,$where=null,$order=null,$group=null,$limit=null)
    {
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($field))
            $this->_Dao_connect->field($field);
        if(!is_null($where))
            $this->_Dao_connect->where($where);
        if(!is_null($order))
            $this->_Dao_connect->order($where);
        if(!is_null($group))
            $this->_Dao_connect->group($group);
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
    public function insert($table,$data=null)
    {
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($data))
            $this->_Dao_connect->data($data);
        else
            # 无效数据对象
            $this->_Error_code = "";
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
    public function update($table,$data=null,$where=null)
    {
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "";
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
            $this->_Dao_connect = "";
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
    public function delete($table,$where)
    {
        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "";
        else
            $this->_Dao_connect->table($table);
        if(!is_null($where))
            $this->_Dao_connect->where($where);
        else
            # 无效条件对象
            $this->_Dao_connect = "";
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
    public function count($table,$where)
    {

        if(is_null($table) or empty($table))
            # 未设置对象表名称
            $this->_Error_code = "";
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