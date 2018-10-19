<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/18
 * Time: 11:33
 */

namespace Origin\Kernel\Transaction\Example;

# 调用数据库封装
use Origin\Kernel\Transaction\Data\Mysql;
use Origin\Kernel\Transaction\Data\Redis;
use Origin\Kernel\Transaction\Data\Mongodb;

class Model
{
    /**
     * @access protected
     * @var string $_Source 数据源
    */
    protected  $_Source = null;
    /**
     * @access protected
     * @var string $_Source_type 数据源类型
    */
    protected $_Source_type = null;
    /**
     * @access public
     * @param string $source
     * @context 构造器用于限定
    */
    function __construct($source=null)
    {
        if (is_null($source)) {
            # 抽取数据库主配置信息内容
            $this->_Source_type = strtolower(Config("DATA_TYPE"));
        } else {
            $this->_Source = $source;
            # 调用多点分布式配置信息内容
            $_mapping = Config("DATA_MATRIX_CONFIG");
            # 遍历配置信息，并执行信息比对
            for ($_i = 0; $_i < count($_mapping); $_i++) {
                if (trim(strtolower($source)) == trim(strtolower($_mapping[$_i]["DATA_NAME"]))) {
                    # 获取对象源类型
                    $this->_Source_type = strtolower($_mapping[$_i]["DATA_TYPE"]);
                    break;
                }
            }
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
    function select($table,$field=null,$where=null,$order=null,$group=null,$limit=null)
    {
        $_receipt = null;
        switch ($this->_Source_type){
            case "redis":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Redis($this->_Source);
                }
                $_receipt = $_dao->select($table,$field);
                break;
            case "mongodb":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mongodb($this->_Source);
                }
                $_receipt = $_dao->select($table,$where);
                break;
            default:
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mysql($this->_Source);
                }
                $_receipt = $_dao->select($table,$field,$where,$order,$group,$limit);
                break;
        }
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $where 条件对象
     * @return mixed
     * @context 查询总数
     */
    function count($table,$where)
    {
        $_receipt = null;
        switch ($this->_Source_type){
            case "redis":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Redis($this->_Source);
                }
                $_receipt = $_dao->count($table,$where);
                break;
            case "mongodb":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mongodb($this->_Source);
                }
                $_receipt = $_dao->count($table,$where);
                break;
            default:
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mysql($this->_Source);
                }
                $_receipt = $_dao->count($table,$where);
                break;
        }
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $data 对象数据
     * @return mixed
     * @context 插入对象数据
     */
    function insert($table,$data=null)
    {
        $_receipt = null;
        switch ($this->_Source_type){
            case "redis":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Redis($this->_Source);
                }
                $_receipt = $_dao->insert($table,$data);
                break;
            case "mongodb":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mongodb($this->_Source);
                }
                $_receipt = $_dao->insert($table,$data);
                break;
            default:
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mysql($this->_Source);
                }
                $_receipt = $_dao->insert($table,$data);
                break;
        }
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $data 对象数据
     * @param string|array $where 条件对象
     * @return mixed
     * @context 更新对象数据
     */
    function update($table,$data=null,$where=null)
    {
        $_receipt = null;
        switch ($this->_Source_type){
            case "redis":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Redis($this->_Source);
                }
                $_receipt = $_dao->update($table,$data,$where);
                break;
            case "mongodb":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mongodb($this->_Source);
                }
                $_receipt = $_dao->update($table,$data);
                break;
            default:
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mysql($this->_Source);
                }
                $_receipt = $_dao->update($table,$data,$where);
                break;
        }
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $table 对象表格
     * @param string|array $where 条件对象
     * @return mixed
     * @context 删除对象数据
     */
    function delete($table,$where)
    {
        $_receipt = null;
        switch ($this->_Source_type){
            case "redis":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Redis($this->_Source);
                }
                $_receipt = $_dao->delete($table,$where);
                break;
            case "mongodb":
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mongodb($this->_Source);
                }
                $_receipt = $_dao->delete($table,$where);
                break;
            default:
                if(is_null($this->_Source)){
                    $_dao = Dao();
                }else{
                    $_dao = Mysql($this->_Source);
                }
                $_receipt = $_dao->delete($table,$where);
                break;
        }
        return $_receipt;
    }
}