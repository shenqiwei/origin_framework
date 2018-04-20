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
    # 只有table
    # 构造函数
    function __construct()
    {
        parent::__construct();
        try{
            # 创建数据库链接地址，端口，应用数据库信息变量
            $_DSN = 'mysql:host='.C('DATA_HOST').';port='.C('DATA_PORT').';dbname='.C('DATA_DB');
            $_username = C('DATA_USER'); # 数据库登录用户
            $_password = C('DATA_PWD'); # 登录密码
            $_option = array(
                # 设置数据库编码规则
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                # 该设置与setAttribute功能相同
//                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
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
    /**
     * 切换数据库源配置信息
     * @access public
     * @param string $connect_name 配置源名称
     * @return object
    */
    function cutConnect($connect_name)
    {
        $_connect_config = C('DATA_MATRIX_CONFIG');
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
        return $this->__getSQL();
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
        # 判断语句直接使用语句
        if(!empty($this->_Query) and $this->_Query != null){
            # 判断语句结构，区分处理，组成新的查询语句
            if(is_true($this->_Regular_Select_Count, strtolower($this->_Query)) === true){
                // 表示为完整的count查询语句
            }elseif(is_true($this->_Regular_Select, strtolower($this->_Query)) === true){
                $this->_Query = 'select count(*) '.substr(strtolower($this->_Query), strpos(strtolower($this->_Query),'from'));
            }elseif(is_true($this->_Regular_from, strtolower($this->_Query)) === true){
                $this->_Query = 'select count(*) '.strtolower($this->_Query);
            }
            try{
                daoLogs($this->_Query);
                # 执行查询搜索
                $_statement = $this->_Connect->query($this->_Query);
                # 返回查询结构
                $_receipt = $_statement->fetch()[0];
                # 释放连接
                $_statement->closeCursor();
            }catch(\PDOException $e) {
                $this->_Connect = null;
                errorLogs($e->getMessage());
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (mysql) Class Error:'.$e->getMessage());
                exit(0);
            }
        }else{
            # 起始结构
            $_sql = 'select ';
            if($this->_Field != null) $_sql .= ' count('.$this->_Field.')';
            else $_sql .= ' count(*)';
            # 表名
            if($this->_Table != null){
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
            if($this->_JoinOn != null) $_sql .= $this->_JoinOn;
            # 复制结构
            if($this->_Union != null) $_sql .= $this->_Union;
            # 条件
            if($this->_Where != null) $_sql .= $this->_Where;
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
                    $_receipt = $_statement->fetch()[0];
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
        # 判断语句直接使用语句
        if(!empty($this->_Query) and $this->_Query != null) {
            if (is_true($this->_Regular_Select, strtolower($this->_Query)) === true) {
                // 表示为完整的查询语句
            }elseif(is_true($this->_Regular_from, strtolower($this->_Query)) === true){
                $this->_Query = 'select * '.strtolower($this->_Query);
            }
            try{
                daoLogs($this->_Query);
                # 执行查询搜索
                $_statement = $this->_Connect->query($this->_Query);
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
        }else {
            # 执行主函数
            # 起始结构
            $_sql = null;
            # top关键字信息 mysql 不支持
//            if($this->_Top != null) $_sql .= $this->_Top;
            # 首条信息 mysql 不支持
//            if($this->_First!= null) $_sql .= $this->_First;
            # 末尾信息 mysql 不支持
//            if($this->_Last!= null) $_sql .= $this->_Last;
            # 求总和
            if($this->_Total != null){
                if($this->_Group!=null)
                    $_sql .= ','.$this->_Total;
                else
                    $_sql .= $this->_Total;
            }
            # 平均数信息 与field冲突，需要group by配合使用
            if($this->_Avg!= null){
                if($this->_Group!=null)
                    $_sql .= ','.$this->_Avg;
                else{
                    if($this->_Total != null)
                        $_sql .= ','.$this->_Avg;
                    else
                        $_sql .= $this->_Avg;
                }
            }
            # 最大值 与field冲突，需要group by配合使用
            if($this->_Max!= null){
                if($this->_Group!=null)
                    $_sql .= ','.$this->_Max;
                else{
                    if($this->_Total != null or $this->_Avg!= null)
                        $_sql .= ','.$this->_Max;
                    else
                        $_sql .= $this->_Max;
                }

            }
            # 最小值 与field冲突，需要group by配合使用
            if($this->_Min!= null){
                if($this->_Group!=null)
                    $_sql .= ','.$this->_Min;
                else{
                    if($this->_Total != null or $this->_Avg!= null or $this->_Max!= null)
                        $_sql .= ','.$this->_Min;
                    else
                        $_sql .= $this->_Min;
                }

            }
            # 求和 与field冲突，需要group by配合使用
            if($this->_Sum!= null){
                if($this->_Group!=null)
                    $_sql .= ','.$this->_Sum;
                else{
                    if($this->_Total != null or $this->_Avg!= null or $this->_Max!= null or $this->_Min!= null)
                        $_sql .= ','.$this->_Sum;
                    else
                        $_sql .= $this->_Sum;
                }

            }
            # 信息大写
            if($this->_UpperCase!= null) $_sql .= $this->_UpperCase;
            # 信息小写
            if($this->_LowerCase!= null) $_sql .= $this->_LowerCase;
            # 信息截取
            if($this->_Mid!= null) $_sql .= $this->_Mid;
            # 信息长度 mysql中使用length
//            if($this->_Len!= null) $_sql .= $this->_Len;
            if($this->_Length!=null) $_sql .= $this->_Length;
            # 信息四舍五入
            if($this->_Round!= null) $_sql .= $this->_Round;
            # 当前数据服务器时间
            if($this->_Now!= null) $_sql .= $this->_Now;
            # 信息格式化
            if($this->_Format!= null) $_sql .= $this->_Format;
            # 单字段不重复值
            if($this->_Distinct != null) $_sql .= $this->_Distinct;
            # 表名
            if($this->_Table != null){
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
            if($this->_JoinOn != null) $_sql .= $this->_JoinOn;
            # 复制结构
            if($this->_Union != null) $_sql .= $this->_Union;
            # 条件
            if($this->_Where != null) $_sql .= $this->_Where;
            # 去重
            if($this->_Group != null) $_sql .= $this->_Group;
            # 排序
            if($this->_Order != null) $_sql .= $this->_Order;
            # 函数调用
            if($this->_Having != null) $_sql .= $this->_Having;
            # 标尺
            if($this->_Limit != null) $_sql .= $this->_Limit;
            # 判定结构然后
            # 字段名信息
            if($this->_Group != null){
                $_sql = $this->_Field.$_sql;
            }else{
                if($this->_Avg == null and $this->_Sum == null and $this->_Max == null and $this->_Min == null and $this->_Total == null) {
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
                    # 返回查询结构
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
        if(!empty($this->_Query) and $this->_Query != null){
            try{
                daoLogs($this->_Query);
                # 执行查询搜索
                $_statement = $this->_Connect->query($this->_Query);
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
        }else{
            # 执行主函数
            $_sql = 'insert into';
            # 表名
            if($this->_Table != null){
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
            if($this->_Data != null){
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
        if(!empty($this->_Query) and $this->_Query != null){
            try{
                daoLogs($this->_Query);
                # 执行查询搜索
                $_statement = $this->_Connect->query($this->_Query);
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
        }else{
            # 执行主函数
            $_sql = 'update';
            # 表名
            if($this->_Table != null){
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
            if($this->_Data != null){
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
            if($this->_Where != null) $_sql .= $this->_Where;
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
        if(!empty($this->_Query) and $this->_Query != null){
            try{
                daoLogs($this->_Query);
                # 执行查询搜索
                $_statement = $this->_Connect->query($this->_Query);
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
        }else{
            # 执行主函数
            $_sql = 'delete ';
            # 表名
            if($this->_Table != null){
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
            if($this->_Where != null) $_sql .= $this->_Where;
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
        }
        # 返回数据
        return $_receipt;
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