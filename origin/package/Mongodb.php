<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架MongoDB封装类（新版本支持包）
 */
namespace Origin\Package;

use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\BulkWrite;
use Mongodb\Driver\WriteConcern;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use Mongodb\Driver\Exception\ConnectionTimeoutException;
use Mongodb\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\WriteException;
use MongoDB\Driver\Exception\WriteConcernException;

class Mongodb
{
    /**
     * SQL基础验证正则表达式变量
     * @var string $_Regular_Name_Confine
     * @var string $_Regular_Comma_Confine_Confine
     */
    protected $_Regular_Name_Confine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)$/';
    protected $_Regular_Comma_Confine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)(\,\s?[^\_\W]+(\_[^\_\W]+)*|\,\`.+[^\s]+\`)*$/';

    /**
     * @var object $_Connect 数据库链接对象
     */
    private $_Connect = null;
    /**
     * @var string $_DB 数据库对象
     */
    protected $_DB = null;
    /**
     * @access public
     * @param string $connect_name 配置源名称
     */
    function __construct($connect_name=null)
    {
        $_connect_config = config('DATA_MATRIX_CONFIG');
        if(is_array($_connect_config)) {
            for ($_i = 0; $_i < count($_connect_config); $_i++) {
                # 判断数据库对象
                if (key_exists("DATA_TYPE", $_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "mongodb"
                    and key_exists("DATA_NAME", $_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name) {
                    $_connect_conf = $_connect_config[$_i];
                    break;
                }
            }
            if(!isset($_connect_conf)) {
                for ($_i = 0; $_i < count($_connect_config); $_i++) {
                    # 判断数据库对象
                    if (key_exists("DATA_TYPE", $_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "mongodb") {
                        $_connect_config = $_connect_config[$_i];
                        break;
                    }
                }
            }else
                $_connect_config = $_connect_conf;
            # 创建数据库链接地址，端口，应用数据库信息变量
            $_mongo_host = $_connect_config['DATA_HOST'];
            $_mongo_port = intval($_connect_config['DATA_PORT']) ? intval($_connect_config["DATA_PORT"]) : 27017;
            if (!empty($_connect_config['DATA_USER']) and !is_null($_connect_config['DATA_USER']))
                $_mongo_user = trim($_connect_config['DATA_USER']);
            if (!empty($_connect_config['DATA_PWD']) and !is_null($_connect_config['DATA_PWD']))
                $_mongo_pwd = trim($_connect_config['DATA_PWD']);
            $_mongo_user_pwd = null;
            if (isset($_mongo_user) and isset($_mongo_pwd))
                $_mongo_user_pwd = $_mongo_user . ":" . $_mongo_pwd . "@";
            $this->_Connect = new Manager("mongodb://" . $_mongo_user_pwd . $_mongo_host . ":" . $_mongo_port);
            $this->_DB = $_connect_config['DATA_DB'];
        }
    }
    /**
     * @var object $_Object
     * 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $_Object = null;
    /**
     * 回传类对象信息
     * @access public
     * @param object $object
     */
    function __setSQL($object)
    {
        $this->_Object = $object;
    }
    /**
     * 获取类对象信息,仅类及其子类能够使用
     * @access public
     * @return object
     */
    protected function __getSQL()
    {
        return $this->_Object;
    }
    /**
     * @var string $_Set 集合名称
     */
    protected $_Set = null;
    /**
     * 集合（表）别名语法
     * @access pblic
     * @param string $table
     * @return object
    */
    function table($table)
    {
        return $this->set($table);
    }
    /**
     * 集合对象约束函数
     * @access public
     * @param string $set
     * @return object
     */
    function set($set)
    {
        $this->_Set = null;
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        if(is_true($this->_Regular_Comma_Confine, $set)){
            $this->_Set = strtolower($set);
        }else{
            try{
                throw new Exception('Set(table) name is not in conformity with the naming conventions');
            }catch(Exception $e){
                exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->_Object;
    }
    /**
     * @var array $_Data 数据数组变量
     */
    protected $_Data = null;
    /**
     * 数据数组方法
     * @access public
     * @param array $data 数据数组
     * @return object
     */
    function data($data)
    {
        $this->_Data = null;
        /**
         * 验证传入值结构，符合数组要求时，进行内容验证
         * @var string $_key
         * @var mixed $_value
         */
        # 判断传入值是否为数组
        if(is_array($data)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            foreach($data as $_key => $_value){
                if(is_true($this->_Regular_Name_Confine, $_key)){
                    $this->_Data[$_key] = $_value;
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new Exception('The column name is not in conformity with the naming rules');
                    }catch(Exception $e){
                        exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }
        }else{
            # 异常处理：参数结构需使用数组
            try{
                throw new Exception('Need to use an array parameter structure');
            }catch(Exception $e){
                exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->_Object;
    }
    /**
     * @var array $_Where 条件数组约束变量
     */
    protected $_Where = null;

    /**
     * 条件约束方法
     * @access public
     * @param string|array $field 条件对象键（条件数组）
     * @param mixed $value 条件值
     * @param string $symbol 运算符号
     * @return object
     */
    function where($field,$value=null,$symbol="eq")
    {
        if(is_array($field)){
            $this->_Where = $field;
        }else{
            if(is_true($this->_Regular_Name_Confine, $field)){
                switch(strtolower(trim($symbol))){
                    case "lt":
                        $this->_Where = array($field=>array("\$lt"=>$value));
                        break;
                    case "gt":
                        $this->_Where = array($field=>array("\$gt"=>$value));
                        break;
                    case "lte":
                        $this->_Where = array($field=>array("\$lte"=>$value));
                        break;
                    case "gte":
                        $this->_Where = array($field=>array("\$gte"=>$value));
                        break;
                    case "in":
                        $this->_Where = array($field=>array("\$in"=>$value));
                        break;
                    case "nin":
                        $this->_Where = array($field=>array("\$nin"=>$value));
                        break;
                    case "like":
                        $this->_Where = array($field=>array("\$regex"=>"{$value}"));
                        break;
                    case "slike":
                        $this->_Where = array($field=>array("\$regex"=>"^{$value}"));
                        break;
                    case "elike":
                        $this->_Where = array($field=>array("\$regex"=>"{$value}^"));
                        break;
                    default:
                        $this->_Where = array($field=>array("\$eq"=>$value));
                }
            }else{
                # 异常处理：字段名不符合SQL命名规则
                try{
                    throw new Exception('The column name is not in conformity with the naming rules');
                }catch(Exception $e){
                    exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $this->_Object;
    }
    /**
     * @var array $_Projection 映射数组约束变量
     */
    protected $_Projection = null;
    /**
     * 映射约束方法
     * @access public
     * @param array $projection 投射参数
     * @return object
     */
    function projection($projection)
    {
        if(is_array($projection)){
            $this->_Projection = $projection;
        }else{
            # 异常处理：条件内容格式不对
            try{
                throw new Exception('Projection format is array');
            }catch(Exception $e){
                exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->_Object;
    }
    /**
     * @var array $_Sort 排序数组约束变量
     */
    protected $_Sort = null;
    /**
     * 排序别名语法
     * @access pblic
     * @param string $field 排序键
     * @param string $type 排序方式
     * @return object
     */
    function order($field,$type)
    {
        return $this->sort($field,$type);
    }
    /**
     * 排序约束方法
     * @access public
     * @param string $field 排序键
     * @param string $type 排序方式
     * @return object
     */
    function sort($field,$type="asc")
    {
        $this->_Sort = null;
        # 使用字符串作为唯一数据类型，通过对参数进行验证，判断参数数据结构，创建排序参数变量
        $_regular_order_confine = '/^(asc|desc)$/';
        # 判断排序信息
        if(is_array($field)){
            $_i = 0;
            foreach($field as $_key => $_type){
                if(is_true($this->_Regular_Name_Confine, $field)){
                    if(is_true($_regular_order_confine, $type)){
                        if($type == "asc")
                            $_type = 1;
                        else
                            $_type = -1;
                    }else
                        $_type = 1;
                    $this->_Sort[$_key] = $_type;
                    $_i++;
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field)){
                if(is_true($_regular_order_confine, $type)){
                    if($type == "asc")
                        $_type = 1;
                    else
                        $_type = -1;
                    $this->_Sort[$field] = $_type;
                }
            }
        }
        return $this->_Object;
    }
    /**
     * @var array $_Limit 显示数量数组约束变量
     */
    protected $_Limit = null;
    /**
     * 显示数量方法
     * @access public
     * @param int $start 标尺起始位置，当不设置length内容时，该参数与length等同
     * @param int $length 显示数量
     * @return object
     */
    function limit($start, $length=0)
    {
        if(is_int($start) and $start >= 0){
            if(is_int($length) and $length > 0){
                $this->_Skip = $start;
                $this->_Limit = $length;
            }else{
                $this->_Limit = $start;
            }
        }
        return $this->_Object;
    }
    /**
     * @var array $_Skip 跳出数量数组约束变量
     */
    protected $_Skip = null;
    /**
     * 跳过数量方法
     * @access public
     * @param array $skip
     * @return object
     */
    function skip($skip)
    {
        $this->_Skip = intval($skip);
        return $this->_Object;
    }
    /**
     * @var boolean $_Multi 执行符合要求更新
     */
    protected $_Multi = false;
    /**
     * 执行同步更新设置函数
     * @access public
     * @param boolean $set
     * @return object
     */
    function multi($set=false)
    {
        if(is_bool($set)){
            $this->_Multi = $set;
        }
        return $this->_Object;
    }
    /**
     * @var boolean $_Upset 执行无效对象新建
     */
    protected $_Upsert = false;
    /**
     * 执行无效对象新建设置函数
     * @access public
     * @param boolean $set
     * @return object
     */
    function upsert($set=false)
    {
        if(is_bool($set)){
            $this->_Upsert = $set;
        }
        return $this->_Object;
    }
    /**
     * @var boolean $_ReadPreference 执行读写分离
     */
    protected  $_ReadPreference = false;
    /**
     * 执行读取分离设置函数
     * @access public
     * @param boolean $set
     * @return object
     */
    function readPreference($set=false)
    {
        if(is_bool($set)){
            $this->_ReadPreference = $set;
        }
        return $this->_Object;
    }
    /**
     * 查询总数
     * @access public
     * @return mixed
     * @throws
     */
    function count()
    {
        $_receipt = null;
        try{
            if(is_null($this->_Where) or !is_array($this->_Where))
                $_where = array();
            else
                $_where = $this->_Where;
            $_option = array();
            if(is_array($this->_Skip))
                $_option["skip"] = $this->_Skip;
            # 调用执行语句驱动类
            $_query = new Query($_where,$_option);
            # 调用Mongo命令函数count运算标明对象集合
            $_command = new Command(array("count"=>$this->_Set,"query"=>$_query));
            # 执行select操作并赋值到返回值变量中
            $_cursor = $this->_Connect->executeCommand($this->_DB,$_command);
            $_receipt = $_cursor->toArray()[0]->n;
        }catch(ConnectionTimeoutException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch (ConnectionException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $_receipt;
    }
    /**
     * 查询
     * @access public
     * @return mixed
     * @throws
     */
    function select()
    {
        $_receipt = null;
        try{
            if(is_null($this->_Where) or !is_array($this->_Where))
                $_where = array();
            else
                $_where = $this->_Where;
            $_option = array();
            if(is_array($this->_Projection))
                $_option["projection"] = $this->_Projection;
            if(is_array($this->_Sort))
                $_option["sort"] = $this->_Sort;
            if(is_array($this->_Limit))
                $_option["limit"] = $this->_Limit;
            if(is_array($this->_Skip))
                $_option["skip"] = $this->_Skip;
            # 调用执行语句驱动类
            $_query = new Query($_where,$_option);
            # 读写分离设置
            $_readPreference = null;
            if($this->_ReadPreference)
                $_readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
            # 执行select操作并赋值到返回值变量中
            $_cursor = $this->_Connect->executeQuery($this->_DB.".".$this->_Set,$_query,$_readPreference);
            # 执行列表转化
            foreach($_cursor as $_document)
            {
                # 转化主返回参数变量
                if(!is_array($_receipt)) $_receipt = array();
                # 传入内容值
                array_push($_receipt,(array)$_document);
            }
        }catch(ConnectionTimeoutException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch (ConnectionException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $_receipt;
    }
    /**
     * 插入
     * @access public
     * @return mixed
     */
    function insert()
    {
        $_receipt = null;
        try{
            # 调用映射id生成类
            $this->_Data["_id"] = new ObjectId();
            # 自定义唯一识别标记
            $this->_Data["_origin_id"] = strval($this->_Data["_id"]);
            # 调用数据写入驱动类
            $_insert = new BulkWrite();
            # 执行写入操作
            $_insert->insert($this->_Data);
            # 调用写入关系类，并设置超时时间
            $_write = new WriteConcern(WriteConcern::MAJORITY,1000);
            # 执行数据写入
            $_result = $this->_Connect->executeBulkWrite($this->_DB.".".$this->_Set,$_insert,$_write);
            # 返回执行参数
            $_receipt = $_result->getInsertedCount();
        }catch (BulkWriteException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(WriteException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(WriteConcernException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $_receipt;
    }
    /**
     * 修改
     * @access public
     * @return mixed
     */
    function update()
    {
        $_receipt = null;
        try{
            $_update = new BulkWrite();
            # 执行更新操作
            $_update->update($this->_Where,array('$set'=>$this->_Data),array("multi"=>$this->_Multi,"upsert"=>$this->_Upsert));
            # 调用写入关系类，并设置超时时间
            $_write = new WriteConcern(WriteConcern::MAJORITY,1000);
            # 执行数据写入
            $_result = $this->_Connect->executeBulkWrite($this->_DB.".".$this->_Set,$_update,$_write);
            # 返回执行参数
            $_receipt = $_result->getModifiedCount();
        }catch (BulkWriteException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(WriteException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(WriteConcernException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $_receipt;
    }
    /**
     * 删除
     * @access public
     * @return mixed
     */
    function delete()
    {
        $_receipt = null;
        try{
            $_update = new BulkWrite();
            # 执行删除操作
            $_update->delete($this->_Where,$this->_Limit);
            # 调用写入关系类，并设置超时时间
            $_write = new WriteConcern(WriteConcern::MAJORITY,1000);
            # 执行数据写入
            $_result = $this->_Connect->executeBulkWrite($this->_DB.".".$this->_Set,$_update,$_write);
            # 返回执行参数
            $_receipt = $_result->getDeletedCount();
        }catch (BulkWriteException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(WriteException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(WriteConcernException $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }catch(Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $_receipt;
    }
}