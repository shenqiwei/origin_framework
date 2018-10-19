<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/18
 * Time: 11:10
 */
namespace Origin\Kernel\Transaction\Data;

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
                $this->_Error_code = "";
            }
        }else{
            $this->_Dao_connect = Redis($source);
        }
    }
    /**
     * @access protected
     * @param string $field 参数对象
     * @param string $column 元素对象
     * @return mixed
     * @context 查询操作
     */
    public function select($field,$column=null)
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
    public function insert($field,$data,$cycle)
    {
        $_i = 0;
        if(is_array($data) and !empty($data)){
            foreach($data as $_field => $_value){
                if(!is_null($this->_Dao_connect->hash()->create($field,$_field,$_value)))
                    $_i++;
            }
            if($cycle)
                $this->_Dao_connect->key()->setSec($field,$cycle);
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
    public function update($field,$data,$cycle)
    {
        $_i = 0;
        if(is_array($data) and !empty($data)){
            foreach($data as $_field => $_value){
                if(!is_null($this->_Dao_connect->hash()->reCreate($field,$_field,$_value)))
                    $_i++;
            }
            if($cycle)
                $this->_Dao_connect->key()->setSec($field,$cycle);
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
    public function delete($field,$column)
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
    public function count($field)
    {
        return $this->_Dao_connect->hash()->len($field);
    }
}