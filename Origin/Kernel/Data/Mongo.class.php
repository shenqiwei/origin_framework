<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Data.Mongo *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2018/08/15 11:05
 * update Time: 2018/08/17 15:03
 * chinese Context: IoC MongoDB封装类（老版本支持包）
 */
namespace Origin\Kernel\Data;

class Mongo
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
     * @var array $_Field 索引字段名数组变量
     */
    protected $_Field = null;
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
     * @var int $_Limit 显示数量数组约束变量
     */
    protected $_Limit = 0;
    /**
     * @var int $_Skip 跳出数量数组约束变量
     */
    protected $_Skip = 0;
    /**
     * @var boolean $_Multi 执行符合要求更新
     */
    protected $_Multi = false;
    /**
     * @var boolean $_Upset 执行无效对象新建
     */
    protected $_Upsert = false;
    /**
     * @access public
     * @param string $connect_name 配置源名称
     */
    function __construct($connect_name=null)
    {
        if(is_null($connect_name)) {
            try {
                # 创建数据库链接地址，端口，应用数据库信息变量
                $_mongo_host = Config('DATA_HOST');
                $_mongo_port = intval(Config('DATA_PORT')) ? intval(Config("DATA_PORT")) : 27017;
                if(!empty(Config('DATA_USER')) and !is_null(Config('DATA_USER')))
                    $_mongo_user = trim(Config('DATA_USER'));
                if(!empty(Config('DATA_PWD')) and !is_null(Config('DATA_PWD')))
                    $_mongo_pwd = trim(Config('DATA_PWD'));
                $_mongo_user_pwd = null;
                if(isset($_mongo_user) and isset($_mongo_pwd))
                    $_mongo_user_pwd = $_mongo_user.":".$_mongo_pwd."@";
                $_mongo_db = Config('DATA_DB');
                $this->_Connect = new \MongoClient("mongodb://" .$_mongo_user_pwd. $_mongo_host . ":" . $_mongo_port);
                $this->_DB = $this->_Connect->$_mongo_db;
            } catch (\MongoConnectionException $e) {
                var_dump(debug_backtrace(0, 1));
                echo("<br />");
                print('Error:' . $e->getMessage());
                exit();
            } catch (\Exception $e) {
                var_dump(debug_backtrace(0, 1));
                echo("<br />");
                print('Error:' . $e->getMessage());
                exit();
            }
        }else{
            $_connect_config = Config('DATA_MATRIX_CONFIG');
            if(is_array($_connect_config)){
                for($_i = 0;$_i < count($_connect_config);$_i++){
                    # 判断数据库类型，框架结构默认系统类型为mysql
                    if(key_exists("DATA_TYPE",$_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "mongo"){
                        # 搜索指向配置信息名称
                        if(key_exists("DATA_NAME",$_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name){
                            try{
                                # 创建数据库链接地址，端口，应用数据库信息变量
                                $_mongo_host =trim($_connect_config[$_i]['DATA_HOST']);
                                $_mongo_port = intval(trim($_connect_config[$_i]['DATA_PORT'])) ? intval(trim($_connect_config[$_i]["DATA_PORT"])): 27017;
                                if(!empty($_connect_config[$_i]['DATA_USER']) and !is_null($_connect_config[$_i]['DATA_USER']))
                                    $_mongo_user = trim($_connect_config[$_i]['DATA_USER']);
                                if(!empty($_connect_config[$_i]['DATA_PWD']) and !is_null($_connect_config[$_i]['DATA_PWD']))
                                    $_mongo_pwd = trim($_connect_config[$_i]['DATA_PWD']);
                                $_mongo_user_pwd = null;
                                if(isset($_mongo_user) and isset($_mongo_pwd))
                                    $_mongo_user_pwd = $_mongo_user.":".$_mongo_pwd."@";
                                $_mongo_db = trim($_connect_config[$_i]['DATA_DB']);
                                $this->_Connect = new \MongoClient("mongodb://".$_mongo_user_pwd. $_mongo_host . ":" . $_mongo_port);
                                $this->_DB = $this->_Connect->$_mongo_db;
                            }catch(\Exception $e){
                                var_dump(debug_backtrace(0,1));
                                echo("<br />");
                                print('Error:'.$e->getMessage());
                                exit();
                            }
                        }
                    }
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
     * @param int $limit
     * @return object
     */
    function limit($limit)
    {
        if(is_array($limit)){
            $this->_Limit = intval($limit);
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
     * @param int $skip
     * @return object
     */
    function skip($skip)
    {
        if(is_array($skip)){
            $this->_Skip = intval($skip);
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
     * @param mixed $operation_type 操作类型
     * @return mixed
     */
    function select($operation_type="more")
    {
        $_receipt = null;
        try{
            if(is_null($this->_Set_Name)){
                throw new \Exception("Set name is null");
            }else{
                if(!is_array($this->_Where))
                    $_where = null;
                else
                    $_where = $this->_Where;
                if(!is_array($this->_Field))
                    $_field = null;
                else
                    $_field = $this->_Field;
                # 获取集合对象信息
                $_set = $this->_Set_Name;
                # 指向集合对象
                $_statement = $this->_DB->$_set;
                # 执行select
                switch($operation_type){
                    case "one":
                    case 2:
                        $_receipt = $_statement->findOne($_where,$_field);
                        break;
                    case "more":
                    case 1:
                    default:
                        $_receipt = $_statement->find($_where,$_field)->skip($this->_Skip)->limit($this->_Limit);
                        break;
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
     * @return object
     */
    function insert()
    {
        $_receipt = null;
        try{
            if(is_null($this->_Set_Name)){
                throw new \Exception("Set name is null");
            }else{
                # 获取集合对象信息
                $_set = $this->_Set_Name;
                # 指向集合对象
                $_statement = $this->_DB->$_set;
                # 执行insert操作
                $_receipt = $_statement->insert($this->_Data);
            }
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
     * @return object
     */
    function update()
    {
        $_receipt = null;
        try{
            if(!is_array($this->_Where))
                $_where = null;
            else
                $_where = $this->_Where;
            # 获取集合对象信息
            $_set = $this->_Set_Name;
            # 指向集合对象
            $_statement = $this->_DB->$_set;
            # 执行update操作
            $_receipt = $_statement->update($_where, array('$set'=>$this->_Data));;
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
     * @return object
     */
    function delete()
    {
        $_receipt = null;
        try{
            if(!is_array($this->_Where))
                $_where = null;
            else
                $_where = $this->_Where;
            # 获取集合对象信息
            $_set = $this->_Set_Name;
            # 指向集合对象
            $_statement = $this->_DB->$_set;
            # 执行delete操作
            $_receipt = $_statement->update($_where, array('$set'=>$this->_Data));;
        }catch(\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $_receipt;
    }
}