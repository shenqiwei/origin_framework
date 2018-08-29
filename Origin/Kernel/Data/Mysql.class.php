<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Data.Mysql *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/01/09 11:04
 * update Time: 2017/01/09 14:59
 * chinese Context: IoC Mysql封装类
 */
namespace Origin\Kernel\Data;
/**
 * 引入公共类函数包
 */
Import('Data:Query');
/**
 * Mysql操作类
*/
class Mysql extends Query
{
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
    function __construct($connect_name=null)
    {
        parent::__construct();
        if(is_null($connect_name) or (empty($connect_name) and !is_numeric($connect_name))){
            try{
                # 创建数据库链接地址，端口，应用数据库信息变量
                $_DSN = 'mysql:host='.Config('DATA_HOST').';port='.Config('DATA_PORT').';dbname='.Config('DATA_DB');
                $_username = Config('DATA_USER'); # 数据库登录用户
                $_password = Config('DATA_PWD'); # 登录密码
                $_option = array(
                    # 设置数据库编码规则
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    \PDO::ATTR_PERSISTENT => true,
                );
                # 创建数据库连接对象
                $this->_Connect = new \PDO($_DSN, $_username, $_password, $_option);
                # 设置数据库参数信息
                $this->_Connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->_Connect->setAttribute(\PDO::ATTR_PERSISTENT,boolval(Config('DATA_P_CONNECT')));
                $this->_Connect->setAttribute(\PDO::ATTR_AUTOCOMMIT,boolval(Config('DATA_AUTO')));
                if(intval(Config('DATA_TIMEOUT')))
                    $this->_Connect->setAttribute(\PDO::ATTR_TIMEOUT,intval(Config('DATA_TIMEOUT')));
                if(intval(Config('DATA_BUFFER')))
                    $this->_Connect->setAttribute(\PDO::MYSQL_ATTR_MAX_BUFFER_SIZE,intval(Config('DATA_BUFFER')));
            }catch(\PDOException $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                print('Error:'.$e->getMessage());
                exit();
            }
        }else{
            $_connect_config = Config('DATA_MATRIX_CONFIG');
            if(is_array($_connect_config)){
                for($_i = 0;$_i < count($_connect_config);$_i++){
                    if(key_exists("DATA_NAME",$_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name){
                        try{
                            # 创建数据库链接地址，端口，应用数据库信息变量
                            $_DSN = 'mysql:host='.$_connect_config[$_i]['DATA_HOST'].';port='.$_connect_config[$_i]['DATA_PORT'].';dbname='.$_connect_config[$_i]['DATA_DB'];
                            $_username = $_connect_config[$_i]['DATA_USER']; # 数据库登录用户
                            $_password = $_connect_config[$_i]['DATA_PWD']; # 登录密码
                            $_option = array(
                                # 设置数据库编码规则
                                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                                \PDO::ATTR_PERSISTENT => true,
                            );
                            # 创建数据库连接对象
                            $this->_Connect = new \PDO($_DSN, $_username, $_password, $_option);
                            # 设置数据库参数信息
                            $this->_Connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                        }catch(\PDOException $e){
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
    /**
     * 返回查询信息的总数
     * @access public
     * @return int
     */
    function count()
    {
        // TODO: Implement count() method.
        # 创建返回信息变量
        $_receipt = null;
        # 起始结构
        $_sql = 'select ';
        if(!is_null($this->_Field)) $_sql .= ' count('.$this->_Field.')';
        else $_sql .= ' count(*)';
        # 表名
        if(!is_null($this->_Table)){
            $_sql .= ' from '.$this->_Table;
        }else{
            # 无有效数据表名称
            try{
                throw new \PDOException('No valid data table name');
            }catch(\PDOException $e){
                $this->_Connect = null;
                errorLogs($e->getMessage());
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql select) Class Error:'.$e->getMessage());
            }
        }
        # 连接结构信息
        if(!is_null($this->_JoinOn)) $_sql .= $this->_JoinOn;
        # 复制结构
        if(!is_null($this->_Union)) $_sql .= $this->_Union;
        # 条件
        if(!is_null($this->_Where)) $_sql .= $this->_Where;
        try{
            daoLogs($_sql);
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            if($_statement === false){
                try{
                    throw new \PDOException('SQL query error!Please check the statement before execution.');
                }catch(\PDOException $e){
                    errorLogs($e->getMessage());
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (mysql) Class Error:'.$e->getMessage());
                    exit(0);
                } finally {
                    $this->_Connect = null;
                }
            }else{
                # 返回查询结构
                $_receipt = $_statement->fetch(\PDO::FETCH_NUM)[0];
                # 释放连接
                $_statement->closeCursor();
            }
        }catch(\PDOException $e){
            $this->_Connect = null;
            errorLogs($e->getMessage());
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (mysql) Class Error:'.$e->getMessage());
            exit(0);
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
        // TODO: Implement select() method.
        # 创建返回信息变量
        $_receipt = null;
        # 执行主函数
        # 起始结构
        $_sql = null;
        # top关键字信息 mysql 不支持
//            if(!is_null($this->_Top)) $_sql .= $this->_Top;
        # 首条信息 mysql 不支持
//            if(!is_null($this->_First)) $_sql .= $this->_First;
        # 末尾信息 mysql 不支持
//            if(!is_null($this->_Last)) $_sql .= $this->_Last;
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
        # 信息大写
        if(!is_null($this->_UpperCase)) $_sql .= $this->_UpperCase;
        # 信息小写
        if(!is_null($this->_LowerCase)) $_sql .= $this->_LowerCase;
        # 信息截取
        if(!is_null($this->_Mid)) $_sql .= $this->_Mid;
        # 信息长度 mysql中使用length
//            if($this->_Len!= null) $_sql .= $this->_Len;
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
        if(!is_null($this->_Table)){
            $_sql .= ' from '.$this->_Table;
        }else{
            # 无有效数据表名称
            try{
                throw new \PDOException('No valid data table name');
            }catch(\PDOException $e){
                $this->_Connect = null;
                errorLogs($e->getMessage());
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql select) Class Error:'.$e->getMessage());
            }
        }
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
        try{
            daoLogs($_sql);
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            if($_statement === false){
                try{
                    throw new \PDOException('SQL query error!Please check the statement before execution.');
                }catch(\PDOException $e){
                    $this->_Connect = null;
                    errorLogs($e->getMessage());
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (mysql) Class Error:'.$e->getMessage());
                    exit(0);
                }
            }else{
                # 回写select查询条数
                $this->_Row_Count = $_statement->rowCount();
                # 返回查询结构
                if($this->_Fetch_Type === 'nv')
                    $_receipt = $_statement->fetchAll(\PDO::FETCH_NUM);
                elseif($this->_Fetch_Type === 'kv')
                    $_receipt = $_statement->fetchAll(\PDO::FETCH_ASSOC);
                else
                    $_receipt = $_statement->fetchAll();
                # 释放连接
                $_statement->closeCursor();
            }
        }catch(\PDOException $e){
            $this->_Connect = null;
            errorLogs($e->getMessage());
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (mysql) Class Error:'.$e->getMessage());
            exit(0);
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
        // TODO: Implement insert() method.
        $_receipt = null;
        # 执行主函数
        $_sql = 'insert into';
        # 表名
        if(!is_null($this->_Table)){
            $_sql .= ' '.$this->_Table;
        }else{
            # 无有效数据表名称
            try{
                throw new \PDOException('No valid data table name');
            }catch(\PDOException $e){
                $this->_Connect = null;
                errorLogs($e->getMessage());
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql select) Class Error:'.$e->getMessage());
            }
        }
        if(!is_null($this->_Data)){
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
        }else{
            # 操作信息无效
            try{
                throw new \PDOException('Operation information is invalid');
            }catch(\PDOException $e){
                $this->_Connect = null;
                errorLogs($e->getMessage());
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql select) Class Error:'.$e->getMessage());
            }
        }
        try{
            daoLogs($_sql);
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            if($_statement === false){
                try{
                    throw new \PDOException('SQL query error!Please check the statement before execution.');
                }catch(\PDOException $e){
                    $this->_Connect = null;
                    errorLogs($e->getMessage());
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (mysql) Class Error:'.$e->getMessage());
                    exit(0);
                }
            }else{
                # 返回查询结构
                $_receipt = $this->_Connect->lastInsertId();
                # 释放连接
                $_statement->closeCursor();
            }
        }catch(\PDOException $e){
            $this->_Connect = null;
            errorLogs($e->getMessage());
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (mysql) Class Error:'.$e->getMessage());
            exit(0);
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
        // TODO: Implement update() method.
        $_receipt = null;
        # 执行主函数
        $_sql = 'update';
        # 表名
        if(!is_null($this->_Table)){
            $_sql .= ' '.$this->_Table;
        }else{
            # 无有效数据表名称
            try{
                throw new \PDOException('No valid data table name');
            }catch(\PDOException $e){
                $this->_Connect = null;
                errorLogs($e->getMessage());
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql select) Class Error:'.$e->getMessage());
            }
        }
        $_sql .= ' set ';
        if(!is_null($this->_Data)){
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
        }else{
            # 操作信息无效
            try{
                throw new \PDOException('Operation information is invalid');
            }catch(\PDOException $e){
                $this->_Connect = null;
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql select) Class Error:'.$e->getMessage());
            }
        }
        # 条件
        if(!is_null($this->_Where)) $_sql .= $this->_Where;
        try{
            daoLogs($_sql);
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            if($_statement === false){
                try{
                    throw new \PDOException('SQL query error!Please check the statement before execution.');
                }catch(\PDOException $e){
                    $this->_Connect = null;
                    errorLogs($e->getMessage());
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (mysql) Class Error:'.$e->getMessage());
                    exit(0);
                }
            }else{
                # 返回查询结构
                $_receipt = $_statement->rowCount();
                # 释放连接
                $_statement->closeCursor();
            }
        }catch(\PDOException $e){
            $this->_Connect = null;
            errorLogs($e->getMessage());
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (mysql) Class Error:'.$e->getMessage());
            exit(0);
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
        // TODO: Implement delete() method.
        $_receipt = null;
        # 执行主函数
        $_sql = 'delete ';
        # 表名
        if(!is_null($this->_Table)){
            $_sql .= 'from '.$this->_Table;
        }else{
            # 无有效数据表名称
            try{
                throw new \PDOException('No valid data table name');
            }catch(\PDOException $e){
                $this->_Connect = null;
                errorLogs($e->getMessage());
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql select) Class Error:'.$e->getMessage());
            }
        }
        # 条件
        if(!is_null($this->_Where)) $_sql .= $this->_Where;
        try{
            daoLogs($_sql);
            # 执行查询搜索
            $_statement = $this->_Connect->query($_sql);
            if($_statement === false){
                try{
                    throw new \PDOException('SQL query error!Please check the statement before execution.');
                }catch(\PDOException $e){
                    $this->_Connect = null;
                    errorLogs($e->getMessage());
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (mysql) Class Error:'.$e->getMessage());
                    exit(0);
                }
            }else{
                # 返回查询结构
                $_receipt = $_statement->rowCount();
                # 释放连接
                $_statement->closeCursor();
            }
        }catch(\PDOException $e){
            $this->_Connect = null;
            errorLogs($e->getMessage());
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (mysql) Class Error:'.$e->getMessage());
            exit(0);
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
        // TODO: Implement count() method.
        # 创建返回信息变量
        $_receipt = null;
        try{
            if(is_true($this->_Regular_Select_Count, strtolower($query)) === true){
                $_select_count = null;
            }elseif(is_true($this->_Regular_Select, strtolower($query)) === true) {
                // 表示为完整的查询语句
            }elseif(is_true($this->_Regular_from, strtolower($query)) === true){
                $query = 'select * '.strtolower($query);
            }
            if(strpos(strtolower($query),"select ") === 0){
                $_query_type = "select";
            }elseif(strpos(strtolower($query),"insert into ") === 0){
                $_query_type = "insert";
            }else{
                $_query_type = "change";
            }
            # 条件运算结构转义
            foreach(array(' gt ' => '>', ' lt ' => '<',' neq ' => '!=', ' eq '=> '=', ' ge ' => '>=', ' le ' => '<=') as $key => $value){
                $query = str_replace($key, $value, $query);
            }
            # 接入执行日志
            daoLogs(trim($query));
            # 执行查询搜索
            $_statement = $this->_Connect->query(trim($query));
            if($_statement === false){
                try{
                    throw new \PDOException('SQL query error!Please check the statement before execution.');
                }catch(\PDOException $e){
                    $this->_Connect = null;
                    errorLogs($e->getMessage());
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (mysql) Class Error:'.$e->getMessage());
                    exit(0);
                }
            }else{
                # 返回查询结构
                if($_query_type === "select"){
                    # 回写select查询条数
                    $this->_Row_Count = $_statement->rowCount();
                    if($this->_Fetch_Type === 'nv')
                        $_receipt = $_statement->fetchAll(\PDO::FETCH_NUM);
                    elseif($this->_Fetch_Type === 'kv')
                        $_receipt = $_statement->fetchAll(\PDO::FETCH_ASSOC);
                    else
                        if(isset($_select_count)){
                            $_receipt = $_statement->fetchAll(\PDO::FETCH_COLUMN)[0];
                        }else{
                            $_receipt = $_statement->fetchAll();
                        }
                }elseif($_query_type === "insert")
                    $_receipt = $this->_Connect->lastInsertId();
                else
                    $_receipt = $_statement->rowCount();
                # 释放连接
                $_statement->closeCursor();
            }
        }catch(\PDOException $e){
            $this->_Connect = null;
            errorLogs($e->getMessage());
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (mysql) Class Error:'.$e->getMessage());
            exit(0);
        }
        return $_receipt;
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