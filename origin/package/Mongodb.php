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
     * @access protected
     * @var Manager|object $Connect 数据库链接对象
     */
    protected $Connect;

    /**
     * @access protected
     * @var string $DB 数据库对象
     */
    protected $DB;

    /**
     * @access protected
     * @var object $Object 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $Object;

    /**
     * @access protected
     * @var string $NameConfine SQL基础验证正则表达式变量
     */
    protected $NameConfine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)$/';

    /**
     * @access protected
     * @var string $CommaConfineConfine SQL基础验证正则表达式变量
     */
    protected $CommaConfine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)(\,\s?[^\_\W]+(\_[^\_\W]+)*|\,\`.+[^\s]+\`)*$/';

    /**
     * 构造函数，预加载数据源配置信息
     * @access public
     * @param string|null $connect_name 配置源名称
     * @return void
     */
    function __construct(?string $connect_name=null)
    {
        $connect_config = config('DATA_MATRIX_CONFIG');
        if(is_array($connect_config)) {
            for ($i = 0; $i < count($connect_config); $i++) {
                # 判断数据库对象
                if (key_exists("DATA_TYPE", $connect_config[$i]) and strtolower(trim($connect_config[$i]["DATA_TYPE"])) === "mongodb"
                    and key_exists("DATA_NAME", $connect_config[$i]) and $connect_config[$i]['DATA_NAME'] === $connect_name) {
                    $connect_conf = $connect_config[$i];
                    break;
                }
            }
            if(!isset($connect_conf)) {
                for ($i = 0; $i < count($connect_config); $i++) {
                    # 判断数据库对象
                    if (key_exists("DATA_TYPE", $connect_config[$i]) and strtolower(trim($connect_config[$i]["DATA_TYPE"])) === "mongodb") {
                        $connect_config = $connect_config[$i];
                        break;
                    }
                }
            }else
                $connect_config = $connect_conf;
            # 创建数据库链接地址，端口，应用数据库信息变量
            $mongo_host = $connect_config['DATA_HOST'];
            $mongo_port = intval($connect_config['DATA_PORT']) ? intval($connect_config["DATA_PORT"]) : 27017;
            if (!empty($connect_config['DATA_USER']))
                $mongo_user = trim($connect_config['DATA_USER']);
            if (!empty($connect_config['DATA_PWD']))
                $mongo_pwd = trim($connect_config['DATA_PWD']);
            $mongo_user_pwd = null;
            if (isset($mongo_user) and isset($mongo_pwd))
                $mongo_user_pwd = $mongo_user . ":" . $mongo_pwd . "@";
            $this->Connect = new Manager("mongodb://" . $mongo_user_pwd . $mongo_host . ":" . $mongo_port);
            $this->DB = $connect_config['DATA_DB'];
        }
    }

    /**
     * 回传类对象信息
     * @access public
     * @param object $object 数据库链接对象
     */
    function __setSQL(object $object)
    {
        $this->Object = $object;
    }

    /**
     * 获取类对象信息,仅类及其子类能够使用
     * @access public
     * @return object 返回链接对象
     */
    protected function __getSQL(): object
    {
        return $this->Object;
    }

    /**
     * @access protected
     * @var string $Set 集合名称
     */
    protected $Set = null;

    /**
     * 集合（表）别名语法
     * @access public
     * @param string $table 表信息
     * @return object 返回链接对象
    */
    function table(string $table)
    {
        return $this->set($table);
    }

    /**
     * 集合对象约束函数
     * @access public
     * @param string $set
     * @return object 返回链接对象
     */
    function set(string $set): object
    {
        $this->Set = null;
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        if(is_true($this->CommaConfine, $set)){
            $this->Set = strtolower($set);
        }else{
            try{
                throw new Exception('Set(table) name is not in conformity with the naming conventions');
            }catch(Exception $e){
                exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->Object;
    }

    /**
     * @access protected
     * @var array $Data 数据数组变量
     */
    protected $Data = null;

    /**
     * 数据数组方法
     * @access public
     * @param array $data 数据数组
     * @return object 返回链接对象
     */
    function data(array $data): object
    {
        $this->Data = null;
        /**
         * 验证传入值结构，符合数组要求时，进行内容验证
         * @var string $key
         * @var mixed $value
         */
        # 判断传入值是否为数组
        if($data){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            foreach($data as $key => $value){
                if(is_true($this->NameConfine, $key)){
                    $this->Data[$key] = $value;
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
        return $this->Object;
    }

    /**
     * @access protected
     * @var array $Where 条件数组约束变量
     */
    protected $Where = null;

    /**
     * 条件约束方法
     * @access public
     * @param string|array $field 条件对象键（条件数组）
     * @param mixed $value 条件值
     * @param string $symbol 运算符号
     * @return object 返回链接对象
     */
    function where($field, $value=null, string $symbol="eq"): object
    {
        if(is_array($field)){
            $this->Where = $field;
        }else{
            if(is_true($this->NameConfine, $field)){
                switch(strtolower(trim($symbol))){
                    case "lt":
                        $this->Where = array($field=>array("\$lt"=>$value));
                        break;
                    case "gt":
                        $this->Where = array($field=>array("\$gt"=>$value));
                        break;
                    case "lte":
                        $this->Where = array($field=>array("\$lte"=>$value));
                        break;
                    case "gte":
                        $this->Where = array($field=>array("\$gte"=>$value));
                        break;
                    case "in":
                        $this->Where = array($field=>array("\$in"=>$value));
                        break;
                    case "nin":
                        $this->Where = array($field=>array("\$nin"=>$value));
                        break;
                    case "like":
                        $this->Where = array($field=>array("\$regex"=>"^$value^"));
                        break;
                    case "slike":
                        $this->Where = array($field=>array("\$regex"=>"^$value"));
                        break;
                    case "elike":
                        $this->Where = array($field=>array("\$regex"=>"$value^"));
                        break;
                    default:
                        $this->Where = array($field=>array("\$eq"=>$value));
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
        return $this->Object;
    }

    /**
     * @access protected
     * @var array $Projection 映射数组约束变量
     */
    protected $Projection = null;

    /**
     * 映射约束方法
     * @access public
     * @param array $projection 投射参数
     * @return object 返回链接对象
     */
    function projection(array $projection): object
    {
        if($projection){
            $this->Projection = $projection;
        }else{
            # 异常处理：条件内容格式不对
            try{
                throw new Exception('Projection format is array');
            }catch(Exception $e){
                exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->Object;
    }

    /**
     * @var array $Sort 排序数组约束变量
     */
    protected $Sort = null;

    /**
     * 排序别名语法
     * @access public
     * @param string $field 排序键
     * @param string $type 排序方式
     * @return object 返回链接对象
     */
    function order(string $field, string $type): object
    {
        return $this->sort($field,$type);
    }

    /**
     * 排序约束方法
     * @access public
     * @param string $field 排序键
     * @param string $type 排序方式
     * @return object 返回链接对象
     */
    function sort(string $field, string $type="asc"): object
    {
        $this->Sort = null;
        # 使用字符串作为唯一数据类型，通过对参数进行验证，判断参数数据结构，创建排序参数变量
        $regular_order_confine = '/^(asc|desc)$/';
        # 判断排序信息
        if(is_array($field)){
            $i = 0;
            foreach($field as $key => $type){
                if(is_true($this->NameConfine, $key)){
                    if(is_true($regular_order_confine, $type)){
                        if($type == "asc")
                            $type = 1;
                        else
                            $type = -1;
                    }else
                        $type = 1;
                    $this->Sort[$key] = $type;
                    $i++;
                }
            }
        }else{
            if(is_true($this->NameConfine, $field)){
                if(is_true($regular_order_confine, $type)){
                    if($type == "asc")
                        $type = 1;
                    else
                        $type = -1;
                    $this->Sort[$field] = $type;
                }
            }
        }
        return $this->Object;
    }

    /**
     * @access protected
     * @var array $Limit 显示数量数组约束变量
     */
    protected $Limit = null;

    /**
     * 显示数量方法
     * @access public
     * @param int $start 标尺起始位置，当不设置length内容时，该参数与length等同
     * @param int $length 显示数量
     * @return object 返回链接对象
     */
    function limit(int $start, int $length=0): object
    {
        if($start >= 0){
            if(is_int($length) and $length > 0){
                $this->Skip = $start;
                $this->Limit = $length;
            }else{
                $this->Limit = $start;
            }
        }
        return $this->Object;
    }

    /**
     * @access protected
     * @var array $Skip 跳出数量数组约束变量
     */
    protected $Skip = null;

    /**
     * 跳过数量方法
     * @access public
     * @param array $skip
     * @return object 返回链接对象
     */
    function skip(array $skip): object
    {
        $this->Skip = intval($skip);
        return $this->Object;
    }

    /**
     * @access protected
     * @var boolean $Multi 执行符合要求更新
     */
    protected $Multi = false;

    /**
     * 执行同步更新设置函数
     * @access public
     * @param boolean $set
     * @return object 返回链接对象
     */
    function multi(bool $set): object
    {
        $this->Multi = $set;
        return $this->Object;
    }

    /**
     * @access protected
     * @var boolean $Upset 执行无效对象新建
     */
    protected $Upsert = false;

    /**
     * 执行无效对象新建设置函数
     * @access public
     * @param boolean $set
     * @return object 返回链接对象
     */
    function upsert(bool $set): object
    {
        $this->Upsert = $set;
        return $this->Object;
    }

    /**
     * @access protected
     * @var boolean $ReadPreference 执行读写分离
     */
    protected  $ReadPreference = false;

    /**
     * 执行读取分离设置函数
     * @access public
     * @param boolean $set
     * @return object 返回链接对象
     */
    function readPreference(bool $set): object
    {
        $this->ReadPreference = $set;
        return $this->Object;
    }

    /**
     * 查询总数
     * @access public
     * @throws
     * @return int
     */
    function count(): int
    {
        try{
            if(is_null($this->Where) or !is_array($this->Where))
                $where = array();
            else
                $where = $this->Where;
            $option = array();
            if(is_array($this->Skip))
                $option["skip"] = $this->Skip;
            # 调用执行语句驱动类
            $query = new Query($where,$option);
            # 调用Mongo命令函数count运算标明对象集合
            $command = new Command(array("count"=>$this->Set,"query"=>$query));
            # 执行select操作并赋值到返回值变量中
            $cursor = $this->Connect->executeCommand($this->DB,$command);
            $receipt = $cursor->toArray()[0]->n;
        }catch(ConnectionTimeoutException | ConnectionException | Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $receipt;
    }

    /**
     * 查询
     * @access public
     * @throws
     * @return array
     */
    function select(): ?array
    {
        $receipt = null;
        try{
            if(is_null($this->Where) or !is_array($this->Where))
                $where = array();
            else
                $where = $this->Where;
            $option = array();
            if(is_array($this->Projection))
                $option["projection"] = $this->Projection;
            if(is_array($this->Sort))
                $option["sort"] = $this->Sort;
            if(is_array($this->Limit))
                $option["limit"] = $this->Limit;
            if(is_array($this->Skip))
                $option["skip"] = $this->Skip;
            # 调用执行语句驱动类
            $query = new Query($where,$option);
            # 读写分离设置
            $readPreference = null;
            if($this->ReadPreference)
                $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
            # 执行select操作并赋值到返回值变量中
            $cursor = $this->Connect->executeQuery($this->DB.".".$this->Set,$query,$readPreference);
            # 执行列表转化
            foreach($cursor as $document)
            {
                # 转化主返回参数变量
                if(!is_array($receipt)) $receipt = array();
                # 传入内容值
                array_push($receipt,(array)$document);
            }
        }catch(ConnectionTimeoutException | ConnectionException | Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $receipt;
    }

    /**
     * 插入
     * @access public
     * @return int
     */
    function insert(): int
    {
        try{
            # 调用映射id生成类
            $this->Data["_id"] = new ObjectId();
            # 自定义唯一识别标记
            $this->Data["_origin_id"] = strval($this->Data["_id"]);
            # 调用数据写入驱动类
            $insert = new BulkWrite();
            # 执行写入操作
            $insert->insert($this->Data);
            # 调用写入关系类，并设置超时时间
            $write = new WriteConcern(WriteConcern::MAJORITY,1000);
            # 执行数据写入
            $result = $this->Connect->executeBulkWrite($this->DB.".".$this->Set,$insert,$write);
            # 返回执行参数
            $receipt = $result->getInsertedCount();
        }catch (BulkWriteException | WriteConcernException | Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $receipt;
    }

    /**
     * 修改
     * @access public
     * @return int
     */
    function update(): int
    {
        try{
            $update = new BulkWrite();
            # 执行更新操作
            $update->update($this->Where,array('$set'=>$this->Data),array("multi"=>$this->Multi,"upsert"=>$this->Upsert));
            # 调用写入关系类，并设置超时时间
            $write = new WriteConcern(WriteConcern::MAJORITY,1000);
            # 执行数据写入
            $result = $this->Connect->executeBulkWrite($this->DB.".".$this->Set,$update,$write);
            # 返回执行参数
            $receipt = $result->getModifiedCount();
        }catch (BulkWriteException | WriteConcernException | Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $receipt;
    }

    /**
     * 删除
     * @access public
     * @return int
     */
    function delete(): int
    {
        try{
            $update = new BulkWrite();
            # 执行删除操作
            $update->delete($this->Where,$this->Limit);
            # 调用写入关系类，并设置超时时间
            $write = new WriteConcern(WriteConcern::MAJORITY,1000);
            # 执行数据写入
            $result = $this->Connect->executeBulkWrite($this->DB.".".$this->Set,$update,$write);
            # 返回执行参数
            $receipt = $result->getDeletedCount();
        }catch (BulkWriteException | WriteConcernException | Exception $e){
            exception("Mongo Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
        return $receipt;
    }
}