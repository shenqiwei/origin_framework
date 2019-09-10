<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Data.Mongodb *
 * version: 1.0 *
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2018
 * @context: IoC MongoDB封装类（新版本支持包）
 */
namespace Origin\Kernel\Data;

class Mongodb
{
    /**
     * @var object $_Connect 数据库链接对象
     */
    private $_Connect = null;
    /**
     * @var object $_Object
     * 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $_Object = null;
    /**
     * @var object $_DB 数据库对象
     */
    protected $_DB = null;
    /**
     * @var string $_Set_Name 集合名称
     */
    protected $_Set_Name = null;
    /**
     * @var array $_Data 数据数组变量
     */
    protected $_Data = null;
    /**
     * @var array $_Where 条件数组约束变量
     */
    protected $_Where = null;
    /**
     * @var array $_Projection 映射数组约束变量
     */
    protected $_Projection = null;
    /**
     * @var array $_Sort 排序数组约束变量
     */
    protected $_Sort = null;
    /**
     * @var array $_Limit 显示数量数组约束变量
     */
    protected $_Limit = null;
    /**
     * @var array $_Skip 跳出数量数组约束变量
     */
    protected $_Skip = null;
    /**
     * @var boolean $_Multi 执行符合要求更新
     */
    protected $_Multi = false;
    /**
     * @var boolean $_Upset 执行无效对象新建
     */
    protected $_Upsert = false;
    /**
     * @var boolean $_ReadPreference 执行读写分离
     */
    protected  $_ReadPreference = false;
    /**
     * @access public
     * @param string $connect_name 配置源名称
     */
    function __construct($connect_name=null)
    {
        $_connect_config = Config('DATA_MATRIX_CONFIG');
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
                        $_connect_conf = $_connect_config[$_i];
                        break;
                    }
                }
            }
            if (isset($_connect_conf)) {
                try {
                    # 创建数据库链接地址，端口，应用数据库信息变量
                    $_mongo_host = $_connect_config[$_i]['DATA_HOST'];
                    $_mongo_port = intval($_connect_conf['DATA_PORT']) ? intval($_connect_conf["DATA_PORT"]) : 27017;
                    if (!empty($_connect_conf['DATA_USER']) and !is_null($_connect_conf['DATA_USER']))
                        $_mongo_user = trim($_connect_conf['DATA_USER']);
                    if (!empty($_connect_conf['DATA_PWD']) and !is_null($_connect_conf['DATA_PWD']))
                        $_mongo_pwd = trim($_connect_conf['DATA_PWD']);
                    $_mongo_user_pwd = null;
                    if (isset($_mongo_user) and isset($_mongo_pwd))
                        $_mongo_user_pwd = $_mongo_user . ":" . $_mongo_pwd . "@";
                    $this->_Connect = new \MongoDB\Driver\Manager("mongodb://" . $_mongo_user_pwd . $_mongo_host . ":" . $_mongo_port);
                    $this->_DB = Config('DATA_DB');
                } catch (\Exception $e) {
                    var_dump(debug_backtrace(0, 1));
                    echo("<br />");
                    print('Error:' . $e->getMessage());
                    exit();
                }
            } else {
                # 无有效数据表名称
                try {
                    throw new \PDOException('Config object is invalid');
                } catch (\PDOException $e) {
                    $this->_Connect = null;
                    errorLogs($e->getMessage());
                    var_dump(debug_backtrace(0, 1));
                    echo("<br />");
                    echo('Origin (mysql select) Class Error:' . $e->getMessage());
                }
            }
        }
    }
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
     * 集合对象约束函数
     * @access public
     * @param string $setName
     * @return object
     */
    function set($setName)
    {
        if(!is_null($setName) and !empty($setName)){
            $this->_Set_Name = $setName;
        }else{
            try{
                throw new \Exception("Null name");
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                print('Error:'.$e->getMessage());
                exit();
            }
        }
        return $this->_Object;
    }
    /**
     * 数据数组方法
     * @access public
     * @param array $data 数据数组
     * @return object
     */
    function data($data)
    {
        if(is_array($data)){
            $this->_Data = $data;
        }else{
            # 异常处理：数组内容格式不对
            try{
                throw new \Exception('Where format is array');
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (Query) Class Error: '.$e->getMessage());
                exit(0);
            }
        }
        return $this->_Object;
    }
    /**
     * 条件约束方法
     * @access public
     * @param array $where 条件内容数组
     * @return object
     */
    function where($where)
    {
        if(is_array($where)){
            $this->_Where = $where;
        }else{
            # 异常处理：条件内容格式不对
            try{
                throw new \Exception('Where format is array');
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (Query) Class Error: '.$e->getMessage());
                exit(0);
            }
        }
        return $this->_Object;
    }
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
                throw new \Exception('Projection format is array');
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (Query) Class Error: '.$e->getMessage());
                exit(0);
            }
        }
        return $this->_Object;
    }
    /**
     * 排序约束方法
     * @access public
     * @param array $sort 排序结构
     * @return object
     */
    function sort($sort)
    {
        if(is_array($sort)){
            $this->_Sort = $sort;
        }else{
            # 异常处理：条件内容格式不对
            try{
                throw new \Exception('Sort format is array');
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (Query) Class Error: '.$e->getMessage());
                exit(0);
            }
        }
        return $this->_Object;
    }
    /**
     * 显示数量方法
     * @access public
     * @param array $limit
     * @return object
     */
    function limit($limit)
    {
        if(is_array($limit)){
            $this->_Limit = $limit;
        }else{
            # 异常处理：条件内容格式不对
            try{
                throw new \Exception('Sort format is array');
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (Query) Class Error: '.$e->getMessage());
                exit(0);
            }
        }
        return $this->_Object;
    }
    /**
     * 跳过数量方法
     * @access public
     * @param array $skip
     * @return object
     */
    function skip($skip)
    {
        if(is_array($skip)){
            $this->_Skip = $skip;
        }else{
            # 异常处理：条件内容格式不对
            try{
                throw new \Exception('Sort format is array');
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (Query) Class Error: '.$e->getMessage());
                exit(0);
            }
        }
        return $this->_Object;
    }
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
     * 创建数据库
     * @access public
     * @return mixed
     * @throws
     */
    function select()
    {
        $_receipt = null;
        try{
            if(is_null($this->_Set_Name)){
                throw new \Exception("Set name is null");
            }else{
                if(is_null($this->_Where) or !is_array($this->_Where))
                    $_where = null;
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
                $_query = new \MongoDB\Driver\Query($_where,$_option);
                # 读写分离设置
                $_readPreference = null;
                if($this->_ReadPreference)
                    $_readPreference = new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::RP_PRIMARY);
                # 执行select操作并赋值到返回值变量中
                $_cursor = $this->_Connect->executeQuery($this->_DB.".".$this->_Set_Name,$_query,$_readPreference);
                # 执行列表转化
                foreach($_cursor as $_document)
                {
                    # 转化主返回参数变量
                    if(!is_array($_receipt)) $_receipt = array();
                    # 传入内容值
                    array_push($_receipt,(array)$_document);
                }
            }
        }catch(\Mongodb\Driver\Exception\ConnectionTimeoutException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch (\Mongodb\Driver\Exception\ConnectionException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $_receipt;
    }
    /**
     * 创建数据库
     * @access public
     * @return mixed
     */
    function insert()
    {
        $_receipt = null;
        try{
            if(is_null($this->_Set_Name)){
                throw new \Exception("Set name is null");
            }else{
                if(!is_array($this->_Data)){
                    throw new \Exception("Data is not array");
                }else{
                    # 调用映射id生成类
                    $_data = array("_id"=>new \MongoDB\BSON\ObjectId());
                    # 拼接数据数组
                    array_merge($_data,$this->_Data);
                    # 调用数据写入驱动类
                    $_insert = new \MongoDB\Driver\BulkWrite();
                    # 执行写入操作
                    $_insert->insert($this->_Data);
                    # 调用写入关系类，并设置超时时间
                    $_write = new \Mongodb\Driver\WriteConcern(\Mongodb\Driver\WriteConcern::MAJORITY,1000);
                    # 执行数据写入
                    $_result = $this->_Connect->executeBulkWrite($this->_DB.".".$this->_Set_Name,$_insert,$_write);
                    # 返回执行参数
                    $_receipt = $_result->getInsertedCount();
                }
            }
        }catch (\MongoDB\Driver\Exception\BulkWriteException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\MongoDB\Driver\Exception\WriteException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\MongoDB\Driver\Exception\WriteConcernException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $_receipt;
    }
    /**
     * 创建数据库
     * @access public
     * @return mixed
     */
    function update()
    {
        $_receipt = null;
        try{
            if(!is_array($this->_Where)){
                throw new \Exception("Where is not array");
            }else{
                if(!is_array($this->_Data)){
                    throw new \Exception("Data is not array");
                }else{
                    $_update = new \MongoDB\Driver\BulkWrite();
                    # 执行更新操作
                    $_update->update($this->_Where,array('$set'=>$this->_Data),array("multi"=>$this->_Multi,"upsert"=>$this->_Upsert));
                    # 调用写入关系类，并设置超时时间
                    $_write = new \Mongodb\Driver\WriteConcern(\Mongodb\Driver\WriteConcern::MAJORITY,1000);
                    # 执行数据写入
                    $_result = $this->_Connect->executeBulkWrite($this->_DB.".".$this->_Set_Name,$_update,$_write);
                    # 返回执行参数
                    $_receipt = $_result->getModifiedCount();
                }
            }
        }catch (\MongoDB\Driver\Exception\BulkWriteException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\MongoDB\Driver\Exception\WriteException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\MongoDB\Driver\Exception\WriteConcernException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $_receipt;
    }
    /**
     * 创建数据库
     * @access public
     * @return mixed
     */
    function delete()
    {
        $_receipt = null;
        try{
            if(!is_array($this->_Where)){
                throw new \Exception("Where is not array");
            }else{
                $_update = new \MongoDB\Driver\BulkWrite();
                # 执行删除操作
                $_update->delete($this->_Where,$this->_Limit);
                # 调用写入关系类，并设置超时时间
                $_write = new \Mongodb\Driver\WriteConcern(\Mongodb\Driver\WriteConcern::MAJORITY,1000);
                # 执行数据写入
                $_result = $this->_Connect->executeBulkWrite($this->_DB.".".$this->_Set_Name,$_update,$_write);
                # 返回执行参数
                $_receipt = $_result->getDeletedCount();
            }
        }catch (\MongoDB\Driver\Exception\BulkWriteException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\MongoDB\Driver\Exception\WriteException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\MongoDB\Driver\Exception\WriteConcernException $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }catch(\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $_receipt;
    }
}