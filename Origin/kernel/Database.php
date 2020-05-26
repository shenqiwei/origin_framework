<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架SQL执行语句封装类
 */
namespace Origin\Kernel;

use Origin\Kernel\Database\Query;
use PDOException;
use PDO;

class Database extends Query
{
    # 数据库连接
    private $_Connect = null;
    # select 为起始词
    private $_Regular_Select = '/^(select)\s(([^\s]+\s)+|\*)\s(from)\s.*/';
    # 带count关键字段
    private $_Regular_Select_Count = '/^(select)\s(count\(([^\s]+\s)+|\*)\)\s(from)\s.*/';
    # from 为起始词
    private $_Regular_from = '/^(from)\s.*/';
    # 获取select查询响应条数信息
    private $_Row_Count = 0;
    # 只有table
    # 构造函数
    function __construct($connect_name=null,$type="mysql")
    {
        if(in_array(strtolower(trim($type)),array("mysql","pgsql","mssql","sqlite","oracle","mariadb"))){
            $this->_Data_Type = strtolower(trim($type));
        }
        $_connect_config = config('DATA_MATRIX_CONFIG');
        if(is_array($_connect_config)){
            for($_i = 0;$_i < count($_connect_config);$_i++){
                if(key_exists("DATA_TYPE",$_connect_config[$_i]) and  strtolower($_connect_config[$_i]['DATA_TYPE']) === $this->_Data_Type
                    and key_exists("DATA_NAME",$_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name){
                    $_connect_conf = $_connect_config[$_i];
                    break;
                }
            }
            if(!isset($_connect_conf)){
                for($_i = 0; $_i < count($_connect_config); $_i++){
                    if((key_exists("DATA_TYPE",$_connect_config[$_i]) and  strtolower($_connect_config[$_i]['DATA_TYPE']) === $this->_Data_Type)
                        or !key_exists("DATA_TYPE",$_connect_config[$_i])){
                        $_connect_config = $_connect_config[$_i];
                        break;
                    }
                }
            }else
                $_connect_config = $_connect_conf;
            switch($this->_Data_Type){
                case "pgsql":
                    $_DSN = "pgsql:host={$_connect_config["DATA_HOST"]};port={$_connect_config["DATA_PORT"]};dbname={$_connect_config["DATA_DB"]}";
                    break;
                case "mssql":
                    $_DSN = "dblib:host={$_connect_config["DATA_HOST"]}:{$_connect_config["DATA_PORT"]};dbname={$_connect_config["DATA_DB"]}";
                    break;
                case "sqlite":
                    $_DSN = "sqlite:{$_connect_config["DATA_DB"]}";
                    break;
                case "oracle":
                    $_oci = "(DESCRIPTION =
                            (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = {$_connect_config["DATA_HOST"]})(PORT = 1521)))
                            (CONNECT_DATA = (SERVICE_NAME = orcl))";
                    $_DSN = "oci:dbname={$_oci}";
                    break;
                case "mariadb":
                default:
                    $_DSN = "mysql:host={$_connect_config["DATA_HOST"]};port={$_connect_config["DATA_PORT"]};dbname={$_connect_config["DATA_DB"]}";
                    break;
            }
            # 创建数据库链接地址，端口，应用数据库信息变量
            $_username = $_connect_config['DATA_USER']; # 数据库登录用户
            $_password = $_connect_config['DATA_PWD']; # 登录密码
            $_option = array(
                # 设置数据库编码规则
                PDO::ATTR_PERSISTENT => true,
            );
            if($this->_Data_Type == "mysql")
               $_option[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
            # 创建数据库连接对象
            $this->_Connect = new PDO($_DSN, $_username, $_password, $_option);
            # 设置数据库参数信息
            $this->_Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # 是否使用持久链接
            $this->_Connect->setAttribute(PDO::ATTR_PERSISTENT,boolval($_connect_config['DATA_P_CONNECT']));
            # SQL自动提交单语句
//                        $this->_Connect->setAttribute(\PDO::ATTR_AUTOCOMMIT,boolval($_connect_conf['DATA_AUTO']));
            # SQL请求超时时间
            if(intval(config('DATA_TIMEOUT')))
                $this->_Connect->setAttribute(PDO::ATTR_TIMEOUT,intval($_connect_config['DATA_TIMEOUT']));
            # SQL是否使用缓冲查询
            if(boolval(config('DATA_USE_BUFFER'))){
                if($this->_Data_Type == "mysql")
                    $this->_Connect->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,boolval($_connect_config['DATA_USE_BUFFER']));
            }

        }
    }

    /**
     * 返回查询信息的总数
     * @access public
     * @return int
     */
    function count()
    {
        # 创建返回信息变量
        $_receipt = null;
        # 起始结构
        $_sql = 'select ';
        if(!is_null($this->_Field)) $_sql .= ' count('.$this->_Field.')';
        else $_sql .= ' count(*)';
        # 表名
        if(!is_null($this->_Table))  $_sql .= ' from '.$this->_Table;
        # 连接结构信息
        if(!is_null($this->_JoinOn)) $_sql .= $this->_JoinOn;
        # 复制结构
        if(!is_null($this->_Union)) $_sql .= $this->_Union;
        # 条件
        if(!is_null($this->_Where)) $_sql .= $this->_Where;
        sLog($_sql);
        try{
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            # 返回查询结构
            $_receipt = $_statement->fetch(PDO::FETCH_NUM)[0];
            # 释放连接
            $_statement->closeCursor();
        }catch(PDOException $e){
            eLog($e->getMessage());
            exception("SQL Error",$this->_Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        # 返回数据
        return $_receipt;
    }
    /**
     * 查询信息函数
     * @access public
     * @return mixed
     */
    function select()
    {
        # 创建返回信息变量
        $_receipt = null;
        # 执行主函数
        # 起始结构
        $_sql = null;
        # top关键字信息 mysql,pgsql 不支持 , mssql语法内容
        if(!is_null($this->_Top)) $_sql .= $this->_Top;
        # 求总和
        if(!is_null($this->_Total)){
            if(!is_null($_sql) or !is_null($this->_Group))
                $_sql .= ','.$this->_Total;
            else
                $_sql .= $this->_Total;
        }
        # 平均数信息 与field冲突，需要group by配合使用
        if(!is_null($this->_Avg)){
            if(!is_null($_sql) or !is_null($this->_Group))
                $_sql .= ','.$this->_Avg;
            else{
                if(!is_null($_sql) or !is_null($this->_Total))
                    $_sql .= ','.$this->_Avg;
                else
                    $_sql .= $this->_Avg;
            }
        }
        # 最大值 与field冲突，需要group by配合使用
        if(!is_null($this->_Max)){
            if(!is_null($_sql) or !is_null($this->_Group))
                $_sql .= ','.$this->_Max;
            else{
                if(!is_null($_sql) or !is_null($this->_Total) or !is_null($this->_Avg))
                    $_sql .= ','.$this->_Max;
                else
                    $_sql .= $this->_Max;
            }

        }
        # 最小值 与field冲突，需要group by配合使用
        if(!is_null($this->_Min)){
            if(!is_null($_sql) or !is_null($this->_Group))
                $_sql .= ','.$this->_Min;
            else{
                if(!is_null($_sql) or !is_null($this->_Total) or !is_null($this->_Avg) or !is_null($this->_Max))
                    $_sql .= ','.$this->_Min;
                else
                    $_sql .= $this->_Min;
            }

        }
        # 求和 与field冲突，需要group by配合使用
        if(!is_null($this->_Sum)){
            if(!is_null($_sql) or !is_null($this->_Group))
                $_sql .= ','.$this->_Sum;
            else{
                if(!is_null($_sql) or !is_null($this->_Total) or !is_null($this->_Avg) or !is_null($this->_Max) or !is_null($this->_Min))
                    $_sql .= ','.$this->_Sum;
                else
                    $_sql .= $this->_Sum;
            }

        }
        # 新增函数支持 begin
        if(!is_null($this->_Abs)) $_sql .= $this->_Abs;
        if(!is_null($this->_Mod)) $_sql .= $this->_Mod;
        if(!is_null($this->_Random)) $_sql .= $this->_Random;
        if(!is_null($this->_L_Trim)) $_sql .= $this->_L_Trim;
        if(!is_null($this->_Trim)) $_sql .= $this->_Trim;
        if(!is_null($this->_R_Trim)) $_sql .= $this->_R_Trim;
        if(!is_null($this->_Replace)) $_sql .= $this->_Replace;
        # 新增函数end
        # 信息大写
        if(!is_null($this->_UpperCase)) $_sql .= $this->_UpperCase;
        # 信息小写
        if(!is_null($this->_LowerCase)) $_sql .= $this->_LowerCase;
        # 信息截取
        if(!is_null($this->_Mid)) $_sql .= $this->_Mid;
        if(!is_null($this->_Length)) $_sql .= $this->_Length;
        # 信息四舍五入
        if(!is_null($this->_Round)) $_sql .= $this->_Round;
        # 当前数据服务器时间
        if(!is_null($this->_Now)) $_sql .= $this->_Now;
        # 信息格式化
        if(!is_null($this->_Format)) $_sql .= $this->_Format;
        # 单字段不重复值
        if(!is_null($this->_Distinct)) $_sql .= $this->_Distinct;
        # 表名
        if(!is_null($this->_Table)) $_sql .= ' from '.$this->_Table;
        if(!is_null($this->_AsTable)) $_sql .=  ' as '.$this->_AsTable;
        # 连接结构信息
        if(!is_null($this->_JoinOn)) $_sql .= $this->_JoinOn;
        # 复制结构
        if(!is_null($this->_Union)) $_sql .= $this->_Union;
        # 条件
        if(!is_null($this->_Where)) $_sql .= $this->_Where;
        # 去重
        if(!is_null($this->_Group)) $_sql .= $this->_Group;
        # 排序
        if(!is_null($this->_Order)) $_sql .= $this->_Order;
        # 函数调用
        if(!is_null($this->_Having)) $_sql .= $this->_Having;
        # 标尺
        if(!is_null($this->_Limit)) $_sql .= $this->_Limit;
        # 判定结构然后
        # 字段名信息
        if(!is_null($this->_Group)){
            $_sql = $this->_Field.$_sql;
        }else{
            if(is_null($this->_Avg) and is_null($this->_Sum) and is_null($this->_Max) and is_null($this->_Min) and is_null($this->_Total)) {
                $_sql = $this->_Field . $_sql;
            }
        }
        # 添加查询头
        $_sql = 'select '.$_sql;
        sLog($_sql);
        try{
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            # 回写select查询条数
            $this->_Row_Count = $_statement->rowCount();
            # 返回查询结构
            if($this->_Fetch_Type === 'nv')
                $_receipt = $_statement->fetchAll(PDO::FETCH_NUM);
            elseif($this->_Fetch_Type === 'kv')
                $_receipt = $_statement->fetchAll(PDO::FETCH_ASSOC);
            else
                $_receipt = $_statement->fetchAll();
            # 释放连接
            $_statement->closeCursor();
        }catch(PDOException $e){
            eLog($e->getMessage());
            exception("SQL Error",$this->_Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        # 返回数据
        return $_receipt;
    }
    /**
     * 插入信息函数
     * @access public
     * @return mixed
     */
    function insert()
    {
        $_receipt = null;
        # 执行主函数
        $_sql = 'insert into';
        # 表名
        if(!is_null($this->_Table)) $_sql .= ' '.$this->_Table;
        $_columns = null;
        $_values = null;
        for($_i = 0; $_i < count($this->_Data); $_i++){
            foreach($this->_Data[$_i] as $_key => $_value){
                if($_i == 0){
                    $_columns = $_key;
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_values = $_value;
                    else
                        $_values = '\''.$_value.'\'';
                }else{
                    $_columns .= ','.$_key;
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_values .= ','.$_value;
                    else
                        $_values .= ',\''.$_value.'\'';
                }
            }
        }
        $_sql .= '('.$_columns.')values('.$_values.')';
        sLog($_sql);
        try{
            # 事务状态
            if(boolval(config("DATA_USE_TRANSACTION")))
                $this->_Connect->beginTransaction();
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            # 返回查询结构
            $_receipt = $this->_Connect->lastInsertId($this->_Primary);
            # 释放连接
            $_statement->closeCursor();
        }catch(PDOException $e){
            eLog($e->getMessage());
            exception("SQL Error",$this->_Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        # 返回数据
        return $_receipt;
    }
    /**
     * 修改信息函数
     * @access public
     * @return mixed
     */
    function update()
    {
        $_receipt = null;
        # 执行主函数
        $_sql = 'update';
        # 表名
        if(!is_null($this->_Table)) $_sql .= ' '.$this->_Table;
        $_sql .= ' set ';
        $_columns = null;
        for($_i = 0; $_i < count($this->_Data); $_i++){
            foreach($this->_Data[$_i] as $_key => $_value){
                if($_i == 0){
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_columns = $_key.'='.$_value;
                    else
                        $_columns = $_key.'=\''.$_value.'\'';
                }else{
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_columns .= ','.$_key.'='.$_value;
                    else
                        $_columns .= ','.$_key.'=\''.$_value.'\'';
                }
            }
        }
        $_sql .= $_columns;
        # 条件
        if(!is_null($this->_Where)) $_sql .= $this->_Where;
        sLog($_sql);
        try{
            # 事务状态
            if(boolval(config("DATA_USE_TRANSACTION")))
                $this->_Connect->beginTransaction();
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            # 返回查询结构
            $_receipt = $_statement->rowCount();
            # 释放连接
            $_statement->closeCursor();
        }catch(PDOException $e){
            eLog($e->getMessage());
            exception("SQL Error",$this->_Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        # 返回数据
        return $_receipt;
    }
    /**
     * 删除信息函数
     * @access public
     * @return mixed
     */
    function delete()
    {
        $_receipt = null;
        # 执行主函数
        $_sql = 'delete ';
        # 表名
        if(!is_null($this->_Table)) $_sql .= 'from '.$this->_Table;
        # 条件
        if(!is_null($this->_Where)) $_sql .= $this->_Where;
        sLog($_sql);
        try{
            # 事务状态
            if(boolval(config("DATA_USE_TRANSACTION")))
                $this->_Connect->beginTransaction();
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            # 返回查询结构
            $_receipt = $_statement->rowCount();
            # 释放连接
            $_statement->closeCursor();
        }catch(PDOException $e){
            eLog($e->getMessage());
            exception("SQL Error",$this->_Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        # 返回数据
        return $_receipt;
    }
    /**
     * 自定义语句执行函数
     * @access public
     * @param string $query
     * @return mixed
     */
    function query($query)
    {
        # 创建返回信息变量
        $_receipt = null;
        if(is_true($this->_Regular_Select_Count, strtolower($query))){
            $_select_count = null;
        }elseif(is_true($this->_Regular_Select, strtolower($query))){
            // 表示为完整的查询语句
            null;
        }elseif(is_true($this->_Regular_from, strtolower($query))){
            $query = 'select * '.strtolower($query);
        }
        if(strpos(strtolower($query),"select ") === 0){
            $_query_type = "select";
        }elseif(strpos(strtolower($query),"insert ") === 0){
            $_query_type = "insert";
        }else{
            $_query_type = "change";
        }
        # 事务状态
        if(boolval(config("DATA_USE_TRANSACTION")) and $_query_type != 'select')
            $this->_Connect->beginTransaction();
        # 条件运算结构转义
        foreach(array('/\s+gt\s+/' => '>', '/\s+lt\s+/ ' => '<','/\s+neq\s+/' => '!=', '/\s+eq\s+/'=> '=', '/\s+ge\s+/' => '>=', '/\s+le\s+/' => '<=') as $key => $value){
            $query = preg_replace($key, $value, $query);
        }
        # 接入执行日志
        sLog(trim($query));
        try{
            # 执行查询搜索
            $_statement = $this->_Connect->query(trim($query));
            # 返回查询结构
            if($_query_type === "select"){
                # 回写select查询条数
                $this->_Row_Count = $_statement->rowCount();
                if($this->_Fetch_Type === 'nv')
                    $_receipt = $_statement->fetchAll(PDO::FETCH_NUM);
                elseif($this->_Fetch_Type === 'kv')
                    $_receipt = $_statement->fetchAll(PDO::FETCH_ASSOC);
                else
                    if(isset($_select_count)){
                        $_receipt = $_statement->fetchAll(PDO::FETCH_COLUMN)[0];
                    }else{
                        $_receipt = $_statement->fetchAll();
                    }
            }elseif($_query_type === "insert")
                $_receipt = $this->_Connect->lastInsertId($this->_Primary);
            else
                $_receipt = $_statement->rowCount();
            # 释放连接
            $_statement->closeCursor();
        }catch(PDOException $e){
            eLog($e->getMessage());
            exception("SQL Error",$this->_Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        return $_receipt;
    }
    /**
     * 执行事务提交
     */
    function getCommit()
    {
        $this->_Connect->commit();
    }
    /**
     * 执行事务回滚
     */
    function getRollBack()
    {
        $this->_Connect->rollBack();
    }
    /**
     * @access public
     * @contact 返回select查询条数信息
     */
    function getRowCount()
    {
        return $this->_Row_Count;
    }
    /**
     * @access public
     * @contact 析构函数：数据库链接释放
     */
    function __destruct()
    {
        $this->_Connect = null;
    }
}