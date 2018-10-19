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
                $this->_Error_code = "";
            }
        }else{
            $this->_Dao_connect = Mongodb($source);
        }
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $where 条件对象
     * @return mixed
     * @context 查询操作
     */
    public function select($table,$where)
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
    public function insert($table,$data)
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
    public function update($table,$data,$where)
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
    public function delete($table,$where)
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
    public function count($table,$where)
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
}