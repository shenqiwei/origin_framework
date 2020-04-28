<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架Sql操作封装类
 */
namespace Origin\Kernel\Database;

use Origin\Kernel\Output;
use Exception;

/**
 * 封装类，数据库操作，主结构访问类
 */
abstract class Query
{
    /**
     * SQL基础验证正则表达式变量
     * @var string $_Regular_Name_Confine
     * @var string $_Regular_Comma_Confine_Confine
     */
    protected $_Regular_Name_Confine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)$/';
    protected $_Regular_Comma_Confine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)(\,\s?[^\_\W]+(\_[^\_\W]+)*|\,\`.+[^\s]+\`)*$/';
    /**
     * @var object $_Object
     * 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $_Object = null;
    /**
     * @var string $_Err_Msg
     * 数据库错误信息变量
    */
    protected $_Err_Msg = null;
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
     * @access protected
     * @var string $_Data_Type 数据源类型
    */
    #
    protected $_Data_Type = "mysql";
    /**
     * @access protected
     * @var string $_Primary 自增主键字段
    */
    protected $_Primary = null;
    /**
     * 设置自增主键字段名信息
     * @access public
     * @param string $field 主键名称
     * @return object
     */
    function setPrimary($field)
    {
        if(is_true($this->_Regular_Name_Confine, $field)){
            $this->_Primary = $field;
        }
        return $this->_Object;
    }
    /**
     * @var string $_Table 数据库表名，
     * 表名与映射结构及Model结构可以同时使用，当使用映射结构和model结构时，表名只做辅助
     */
    protected $_Table = null;
    protected $_AsTable = null;
    /**
     * 表名获取方法
     * @access public
     * @param string $table
     * @param string $table_as
     * @return object
     */
    function table($table,$table_as=null)
    {
        # 初始化所有参与项
        $this->_Table = null; # 主表名
        $this->_AsTable = null; # 主表别名
        $this->_JoinOn = null; # 关联结构式
        $this->_Top = null; # sql语法top
        $this->_Total = null; # count 语法结构式
        $this->_Field = "*"; # field语法结构式
        $this->_Distinct = null; # 不重复结构式
        $this->_Union = null; # 相同合并结构式
        $this->_Data = array(); # 提交数据结构式用于支持insert和update
        $this->_Where = null; # 条件结构式
        $this->_Group = null; # 分组结构式
        $this->_Abs = null; # 求正整数
        $this->_Avg = null; # 求平均数
        $this->_Max = null; # 求最大值
        $this->_Min = null; # 求最小值
        $this->_Sum = null; # 求数值综合综合
        $this->_Mod = null; # 取余结构式
        $this->_Random = null; # 随机数结构式
        $this->_L_Trim = null; # 去左空格或指定符号内容
        $this->_Trim = null; # 去两侧空格或指定符号内容
        $this->_R_Trim = null; # 去右空格或指定符号内容
        $this->_Replace = null; # 替换指定符号内容
        $this->_UpperCase = null; # 全大写
        $this->_LowerCase = null; # 全小写
        $this->_Mid = null; # 截取字段
        $this->_Length = null; # 求字符长度
        $this->_Round = null; # 四舍五入
        $this->_Now = null; # 当前服务器时间
        $this->_Format = null; # 格式化显示数据
        $this->_Having = null; # 类似where可控语法函数
        $this->_Order = null; # 排序
        $this->_Limit = null; # 显示范围
        $this->_Fetch_Type = "all"; # 显示类型
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        if(is_true($this->_Regular_Comma_Confine, $table)){
            $this->_Table = strtolower($table);
            if(!is_null($table_as) and is_true($this->_Regular_Comma_Confine, $table_as)){
                $this->_AsTable = $table_as;
            }
        }else{
            try{
                throw new Exception('Table name is not in conformity with the naming conventions');
            }catch(Exception $e){
                $_output = new Output();
                $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_JoinOn 多表联合匹配条件
     */
    protected $_JoinOn = null;
    /**
     * 多表关系匹配 join 语句，支持多表联查，根据join特性join后接表名为单表
     * 多表联合匹配条件 on，与join联合使用，当field只有一个值时，系统会自动调用表格中，同名字段名
     * 当有多个条件时，可以使用数组进行结构导入
     * @access public
     * @param string $join_table
     * @param string $join_field
     * @param string $major_field
     * @param string $join_table_as
     * @param string $join_type
     * @return object
     */
    function join($join_table,$join_field,$major_field,$join_table_as=null,$join_type=null)
    {
        $_join_type = null;
        if(in_array(strtolower(trim($join_type)),array("inner","left","right")))
            $_join_type = strtolower(trim($join_type))." ";
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        if (is_true($this->_Regular_Comma_Confine, $join_table)) {
            # 根据SQL数据库命名规则判断字段名是否符合规则要求，如果符合装在进SQL模块Field变量中
            if (is_true($this->_Regular_Comma_Confine, $join_field)) {
                if (is_true($this->_Regular_Comma_Confine, $major_field)) {
                    if(is_null($join_table_as)){
                        if(is_null($this->_AsTable))
                            $this->_JoinOn .= " {$_join_type}join {$join_table} on {$join_table}.{$join_field} = {$this->_Table}.{$major_field}";
                        else
                            $this->_JoinOn .= " {$_join_type}join {$join_table} on {$join_table}.{$join_field} = {$this->_AsTable}.{$major_field}";
                    }else{
                        if(is_null($this->_AsTable))
                            $this->_JoinOn .= " {$_join_type}join {$join_table} as {$join_table_as} on {$join_table_as}.{$join_field} = {$this->_Table}.{$major_field}";
                        else
                            $this->_JoinOn .= " {$_join_type}join {$join_table} as {$join_table_as} on {$join_table_as}.{$join_field} = {$this->_AsTable}.{$major_field}";
                    }
                } else {
                    # 异常处理：字段名称不符合命名规范
                    try {
                        throw new Exception('Field name is not in conformity with the naming conventions');
                    } catch (Exception $e) {
                        $_output = new Output();
                        $_output->exception("Query Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            } else {
                # 异常处理：字段名称不符合命名规范
                try {
                    throw new Exception('Field name is not in conformity with the naming conventions');
                } catch (Exception $e) {
                    $_output = new Output();
                    $_output->exception("Query Error", $e->getMessage(), debug_backtrace(0, 1));
                    exit();
                }
            }
        } else {
            try {
                throw new Exception('Join Table name is not in conformity with the naming conventions');
            } catch (Exception $e) {
                $_output = new Output();
                $_output->exception("Query Error", $e->getMessage(), debug_backtrace(0, 1));
                exit();
            }
        }
        return $this->__getSQL();
    }
    /**
     * 多表关系匹配 inner join 语句，支持多表联查，根据join特性join后接表名为单表
     * 多表联合匹配条件 on，与join联合使用，当field只有一个值时，系统会自动调用表格中，同名字段名
     * 当有多个条件时，可以使用数组进行结构导入
     * @access public
     * @param string $join_table
     * @param string $join_field
     * @param string $major_field
     * @param string $join_table_as
     * @return object
     */
    function iJoin($join_table,$join_field,$major_field,$join_table_as=null)
    {
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        $this->join($join_table,$join_field,$major_field,$join_table_as,"inner");
        return $this->__getSQL();
    }
    /**
     * 多表关系匹配 left join 语句，支持多表联查，根据join特性join后接表名为单表
     * 多表联合匹配条件 on，与join联合使用，当field只有一个值时，系统会自动调用表格中，同名字段名
     * 当有多个条件时，可以使用数组进行结构导入
     * @access public
     * @param string $join_table
     * @param string $join_field
     * @param string $major_field
     * @param string $join_table_as
     * @return object
     */
    function lJoin($join_table,$join_field,$major_field,$join_table_as=null)
    {
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        $this->join($join_table,$join_field,$major_field,$join_table_as,"left");
        return $this->__getSQL();
    }
    /**
     * 多表关系匹配 right join 语句，支持多表联查，根据join特性join后接表名为单表
     * 多表联合匹配条件 on，与join联合使用，当field只有一个值时，系统会自动调用表格中，同名字段名
     * 当有多个条件时，可以使用数组进行结构导入
     * @access public
     * @param string|array $join_table
     * @param string $join_field
     * @param string $major_field
     * @param string $join_table_as
     * @return object
     */
    function rJoin($join_table,$join_field,$major_field,$join_table_as=null)
    {
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        $this->join($join_table,$join_field,$major_field,$join_table_as,"right");
        return $this->__getSQL();
    }
    /**
     * @var string $_Top
     * 用于select查询中Top应用
     */
    protected $_Top = null;
    /**
     * Top 语句结构
     * @access public
     * @param int $number
     * @param boolean $percent
     * @return object
     */
    function top($number, $percent=false)
    {
        switch($this->_Data_Type){
            case "mssql":
                # top 关键字后边只能接数组，所以在拼接语句时，要对number进行类型转化
                $this->_Top .=' top '.intval($number);
                # 判断是否使用百分比进行查询
                if($percent and intval($number) > 100)
                    $this->_Top = ' top 100 percent';
                elseif($percent and intval($number) <= 100)
                    $this->_Top .= ' percent';
                break;
            default:
                break;
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Total
     * 用于select查询中求总数，语句结构利用count函数
     */
    protected $_Total = null;
    /**
     * 返回指定字段下所有列值的总和，只支持数字型字段列
     * @access public
     * @param string $field
     * @return object
     */
    function total($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Total .= "{$_symbol}count({$field[$_i]})";
                }else{
                    $this->_Total .= "{$_symbol}count(".array_keys($field)[$_i].") as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Total = "count({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Field 查询元素
     * 用于在select查询中精确查寻数据, 支持数组格式，同时支持as关键字
     */
    protected $_Field = '*';
    /**
     * 查询字段名，默认信息是符号（*）
     * 当传入值是数组时，$key为原字段名，$value为简写名
     * @access public
     * @param mixed $field
     * @return object
     */
    function field($field)
    {
        /**
         * 进行传入值结构判断，如果传入值为数组并且数组元素总数大于0
         * @var int $i; 计数变量
         * @var string $_key 数组键值变量
         * @var string $_value 数组元素值变量
         */
        # 判断field值类型是否为数组
        if(is_array($field)){
            # 判断数组大小是否大于0
            if(count($field)){
                # 创建计数变量
                $i=0;
                # 使用foreach函数进行数组遍历
                foreach($field as $_key => $_value){
                    # 判断字段名和简名是否和规
                    if(is_true($this->_Regular_Comma_Confine, $_value)) {
                        if(is_true($this->_Regular_Comma_Confine, $_key) and !is_numeric($_key)){
                            # 判断计数变量当前值，等于0时不添加连接符号
                            if ($i == 0)
                                $this->_Field = " {$_key} as {$_value}";
                            else
                                $this->_Field .= ",{$_key} as {$_value}";
                        }else{
                            if($i == 0)
                                $this->_Field = $_value;
                            else
                                $this->_Field .= ",{$_value}";
                        }
                        $i += 1;
                    }else{
                        try{
                            throw new Exception('Field name is not in conformity with the naming conventions');
                        }catch(Exception $e){
                            $_output = new Output();
                            $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }
                }
            }
        }else{
            # 根据SQL数据库命名规则判断字段名是否符合规则要求，如果符合装在进SQL模块Field变量中
            if(is_true($this->_Regular_Comma_Confine, $field))
                $this->_Field = $field;
            else{
                # 异常处理：字段名称不符合命名规范
                try{
                    throw new Exception('Field name is not in conformity with the naming conventions');
                }catch(Exception $e){
                    $_output = new Output();
                    $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Distinct 查询单字段不重复值
     */
    protected $_Distinct = null;
    /**
     * 列出单列字段名不同值 distinct 语句，该语句仅支持单列字段显示，如果需要显示多列信息，需要时group
     * 在一些应用场景中，可以把distinct看作是group的简化功能结构
     * @access public
     * @param string $field
     * @return object
     */
    function distinct($field)
    {
        /**
         * 进行传入值结构判断
         */
        if(is_true($this->_Regular_Name_Confine, $field))
            $this->_Distinct = ",distinct {$field}";
        return $this->__getSQL();
    }
    /**
     * @var string $_Union 低效相同列，相同数，支持单个或多个
     */
    protected $_Union = null;
    /**
     * 对多个查询语句的相同字段中的相同数据进行合并,当前版本仅支持两个表单字段查询，并对输入结构进行验证和隔离
     * @access public
     * @param string $table
     * @param string $field
     * @return object
     */
    function union($table, $field)
    {
        $this->_Union = null;
        /**
         * 使用SQL命名规则对输入的表名和字段名进行验证
         */
        # 判断传入参数表名和字段名是否符合命名规则
        if(is_true($this->_Regular_Name_Confine, $table) and is_true($this->_Regular_Name_Confine, $field))
            $this->_Union = " union select {$field} from {$table}";
        else{
            if(is_true($this->_Regular_Name_Confine, $table)){
                try{
                    throw new Exception('The field name is not in conformity with the SQL naming rules');
                }catch(Exception $e){
                    $_output = new Output();
                    $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }else{
                # 异常处理：表名名不符合SQL命名规则
                try{
                    throw new Exception('The table name is not in conformity with the SQL naming rules');
                }catch(Exception $e){
                    $_output = new Output();
                    $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var array $_Data
     * 用户存储需要修改或者添加的数据信息，该模块与验证模块连接使用
     */
    protected $_Data = array();
    /**
     * 添加修改值获取方法,传入值结构为数组，数组key为字段名，数组value为传入值
     * @access public
     * @param array $field
     * @return object
     */
    function data($field)
    {
        /**
         * 验证传入值结构，符合数组要求时，进行内容验证
         * @var string $_key
         * @var mixed $_value
         */
        # 判断传入值是否为数组
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            foreach($field as $_key => $_value){
                if(is_true($this->_Regular_Name_Confine, $_key)){
                    if(!is_array($this->_Data)) $this->_Data = array();
                    array_push($this->_Data, array($_key => $_value));
                    # this->_Data[$_key] = $_value;
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(Exception $e){
                        $_output = new Output();
                        $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }
        }else{
            # 异常处理：参数结构需使用数组
            try{
                throw new Exception('Need to use an array parameter structure');
            }catch(Exception $e){
                $_output = new Output();
                $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Where
     * sql语句条件变量，分别为两种数据类型，当为字符串时，直接引用，当为数组时，转化执行
     */
    protected $_Where = null;
    /**
     * 条件信息加载方法，传入值类型支持字符串、数组，数组结构可以为多级数组
     * 1.当数组key为字段名，数组value为条件值，条件表述为等于条件，若数组value为数组结构
     * 2.当数组key为特定字符串（$and 或 $or）,数组value必须为数组结构，数组结构表述与表述 1要求相同
     * 3.数组关系结构中，同级条件结构放在同一个上级数组内容
     * 3.当为字符串，要求条件信息符合SQL语句规则
     * @access public
     * @param mixed $field
     * @return object
     */
    function where($field=null)
    {
        /**
         * 区别数据类型使用SQL命名规则对输入的字段名进行验证
         */
        if(is_array($field)){# 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            $this->_Where = " where ".$this->multiWhere($field);
        }else{
            # 对输入字符串进行特殊字符转义，降低XSS攻击
            # 用预设逻辑语法数组替代特殊运算符号
            if(!is_null($field) and !empty($field)){
                foreach(array('/\s+gt\s+/' => '>', '/\s+lt\s+/ ' => '<','/\s+neq\s+/' => '!=', '/\s+eq\s+/'=> '=', '/\s+ge\s+/' => '>=', '/\s+le\s+/' => '<=','/\s+in\s+/'=>'in','/\s+nin\s+/'=>"not in") as $key => $value){
                    $field = preg_replace($key, $value, $field);
                }
                $this->_Where = " where {$field}";
            }
        }
        return $this->__getSQL();
    }
    /**
     * 条件拆分函数
     * @access private
     * @param array $where
     * @return string
    */
    private function multiWhere($where)
    {
        $_where = null;
        if(is_array($where)){
            $_is_multi = false;
            if(count($where) > 1) $_is_multi = true;
            foreach($where as $_key => $_value) {
                if ($_key == "\$and") {
                    if ($_is_multi)
                        $_where .= " and (" . $this->multiWhere($_value) . ")";
                    else
                        $_where .= " and " . $this->multiWhere($_value);
                } elseif ($_key == "\$or") {
                    if ($_is_multi)
                        $_where .= " or (" . $this->multiWhere($_value) . ")";
                    else
                        $_where .= " or " . $this->multiWhere($_value);
                } elseif (is_true($this->_Regular_Name_Confine, $_key)) {
                    if (is_array($_value)) {
                        $_first_key = array_keys($_value)[0];
                        $_symbol = "=";
                        switch ($_first_key) {
                            case "\$eq":
                                $_symbol = "=";
                                break;
                            case "\$lt":
                                $_symbol = "<";
                                break;
                            case "\$gt":
                                $_symbol = ">";
                                break;
                            case "\$in":
                                $_symbol = "in";
                                break;
                            case "\$le":
                                $_symbol = "<=";
                                break;
                            case "\$ge":
                                $_symbol = ">=";
                                break;
                            case "\$neq":
                                $_symbol = "!=";
                                break;
                            case "\$nin":
                                $_symbol = "not in";
                                break;
                            default:
                                continue;
                        }
                        if (is_null($_where)) {
                            if (is_integer($_value) or is_float($_value) or is_double($_value)) {
                                $_where = " {$_key} {$_symbol} {$_value}";
                            } else {
                                $_where = " {$_key} {$_symbol} '{$_value}'";
                            }
                        } else {
                            if (is_int($_value) or is_float($_value) or is_double($_value)) {
                                $_where .= " and {$_key} {$_symbol} {$_value}";
                            } else {
                                $_where .= " and {$_key} {$_symbol} '{$_value}'";
                            }
                        }
                    } else {
                        # 将数组信息存入类变量
                        if (is_null($_where)) {
                            if (is_integer($_value) or is_float($_value) or is_double($_value)) {
                                $_where = " {$_key} = {$_value}";
                            } else {
                                $_where = " {$_key} = '{$_value}'";
                            }
                        } else {
                            if (is_int($_value) or is_float($_value) or is_double($_value)) {
                                $_where .= " and {$_key} = {$_value}";
                            } else {
                                $_where .= " and {$_key} = '{$_value}'";
                            }
                        }
                    }
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(Exception $e){
                        $_output = new Output();
                        $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }
        }
        return $_where;
    }
    /**
     * @var string $_Group
     * 分组变量，与where功能支持相似
     */
    protected $_Group = null;
    /**
     * 去重（指定字段名）显示列表信息
     * @access public
     * @param mixed $field
     * @return object
     */
    function group($field)
    {
        /**
         * 区别数据类型使用SQL命名规则对输入的字段名进行验证
         * @var string|int $_key
         * @var mixed $_value
         */
        # 判断传入参数类型
        if(is_array($field)){
            # 创建编辑变量
            $_i = 0;
            # 循环遍历数组内元素信息
            foreach($field as  $_key => $_value){
                # 验证元素信息值是否符合SQL命名规则
                if(is_true($this->_Regular_Name_Confine, $_value)){
                    # 拼接条件信息
                    if($_i == 0)
                        $this->_Group = " group by {$_value}";
                    else
                        $this->_Group .= ",{$_value}";
                    $_i++;
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(Exception $e){
                        $_output = new Output();
                        $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }
        }else{
            # 使用多条件结构正则验证字符串内容
            if(is_true($this->_Regular_Comma_Confine, $field))
                $this->_Group = " group by {$field}";
            else{
                # 异常处理：GROUP语法字段名结构不符合SQL使用规则
                try{
                    throw new Exception('Group of grammatical structure of the field name is not in
                                               conformity with the SQL using rules');
                }catch(Exception $e){
                    $_output = new Output();
                    $_output->exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Abs
     * 求正整数
    */
    protected $_Abs = null;
    /**
     * 求正WW数值
     * @access public
     * @param mixed $field
     * @return object
    */
    function abs($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Abs .= "{$_symbol}abs({$field[$_i]})";
                }else{
                    $this->_Abs .= "{$_symbol}abs(".array_keys($field)[$_i].") as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Abs = "abs({$field})";
        }
        return $this->_Object;
    }
    /**
     * @var string $_Avg
     * 求平均数函数的字段名
     */
    protected $_Avg = null;
    /**
     * 查询语句指定字段值平均数，支持单字段名
     * @access public
     * @param mixed $field
     * @return object
     */
    function avg($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Avg .= "{$_symbol}avg({$field[$_i]})";
                }else{
                    $this->_Avg .= "{$_symbol}avg(".array_keys($field)[$_i].") as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Avg = "avg({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Max
     * 指定字段下最大记录值的字段名
     */
    protected $_Max = null;
    /**
     * 查询语句指定字段中最大值，支持单字段名
     * @access public
     * @param mixed $field
     * @return object
     */
    function max($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Max .= "{$_symbol}max({$field[$_i]})";
                }else{
                    $this->_Max .= "{$_symbol}max(".array_keys($field)[$_i].") as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Max = "max({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_min
     * 指定字段下最小记录值的字段名
     */
    protected $_Min = null;
    /**
     * 查询语句指定字段中最小值，支持单字段名
     * @access public
     * @param mixed $field
     * @return object
     */
    function min($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Min .= "{$_symbol}min({$field[$_i]})";
                }else{
                    $this->_Min .= "{$_symbol}min(".array_keys($field)[$_i].") as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Min = "min({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Sum
     * 计算字段下所有列数值总和的字段名
     */
    protected $_Sum = null;
    /**
     * 返回指定字段下所有列值的总和，只支持数字型字段列
     * @access public
     * @param mixed $field
     * @return object
     */
    function sum($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Sum .= "{$_symbol}sum({$field[$_i]})";
                }else{
                    $this->_Sum .= "{$_symbol}sum(".array_keys($field)[$_i].") as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Sum = "sum({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * 取余
     * @var string $_Mod
    */
    protected $_Mod = null;
    /**
     * 取余
     * @access public
     * @param mixed $field
     * @param int second
     * @return object
    */
    protected function mod($field,$second=0)
    {
        switch($this->_Data_Type){
            case "sqlite":
                null;
                break;
            case "mssql":
            case "mariadb":
            case "oracle":
            case "pgsql":
            default:
                if(is_array($field)){
                    for($_i=0;$_i<count($field);$_i++){
                        if(!key_exists("as_name",$field[$_i])){
                            $this->_Mod = ",mod({$field[$_i]["first"]},{$field[$_i]["second"]})";
                        }else{
                            $this->_Mod = ",mod({$field[$_i]["first"]},{$field[$_i]["second"]}}) as {$field[$_i]["as_name"]}";
                        }
                    }
                }else{
                    if(is_true($this->_Regular_Name_Confine, $field))
                        $this->_Mod = ", mod({$field},{$second})";
                }
                break;
        }
        return $this->__getSQL();
    }
    /**
     * 求随机数
     * @var string $_Random
    */
    protected $_Random = null;
    /**
     * 求随机数
     * @access public
     * @return object
    */
    protected function random()
    {
        switch ($this->_Data_Type){
            case "pgsql":
            case "sqlite":
                $this->_Random = ",random()";
                break;
            case "oracle":
                $this->_Random = ",dbms_random.value";
                break;
            case "mssql":
            case "mariadb":
            default:
                $this->_Random = ",rand()";
                break;
        }
        return $this->_Object;
    }
    /**
     * 去除左边指定字符（空格）
     * @var string $_L_Trim;
    */
    protected $_L_Trim = null;
    /**
     * 去除左边指定字符（空格）
     * @access public
     * @param mixed $field
     * @param string $str
     * @return object
    */
    protected function lTrim($field,$str=null)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_str = null;
                switch ($this->_Data_Type){
                    case "pgsql":
                    case "sqlite":
                    case "oracle":
                        if(!is_null($field[$_i]["str"]))
                            $_str = ",{$field[$_i]["str"]}";
                        break;
                    case "mssql":
                    case "mariadb":
                    default:
                        null;
                        break;
                }
                if(!key_exists("as_name",$field[$_i])){
                    $this->_L_Trim = ",ltrim({$field[$_i]["field"]}{$_str})";
                }else{
                    $this->_L_Trim = ",ltrim({$field[$_i]["field"]}{$_str}) as ".$field[$_i]["as_name"];
                }
            }
        }else{
            $_str = null;
            switch ($this->_Data_Type){
                case "pgsql":
                case "sqlite":
                case "oracle":
                    if(!is_null($str))
                        $_str = ",{$str}";
                    break;
                case "mssql":
                case "mariadb":
                default:
                    null;
                    break;
            }
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_L_Trim = ",ltrim({$field}{$_str}))";
        }
        return $this->__getSQL();
    }
    /**
     * 去除指定字符（空格）
     * @var string $_Trim;
     */
    protected $_Trim = null;
    /**
     * 去除指定字符（空格）
     * @access public
     * @param mixed $field
     * @param string $str
     * @return object
     */
    protected function trim($field,$str=null)
    {
        if($this->_Data_Type != "mssql"){
            if(is_array($field)){
                for($_i=0;$_i<count($field);$_i++){
                    $_str = null;
                    switch ($this->_Data_Type){
                        case "pgsql":
                        case "sqlite":
                        case "oracle":
                            if(!is_null($field[$_i]["str"]))
                                $_str = ",{$field[$_i]["str"]}";
                            break;
                        case "mariadb":
                        default:
                            null;
                            break;
                    }
                    if(!key_exists("as_name",$field[$_i])){
                        $this->_Trim = ",trim({$field[$_i]["field"]}{$_str})";
                    }else{
                        $this->_Trim = ",trim({$field[$_i]["field"]}{$_str}) as ".$field[$_i]["as_name"];
                    }
                }
            }else{
                $_str = null;
                switch ($this->_Data_Type){
                    case "pgsql":
                    case "sqlite":
                    case "oracle":
                        if(!is_null($str))
                            $_str = ",{$str}";
                        break;
                    case "mariadb":
                    default:
                        null;
                        break;
                }
                if(is_true($this->_Regular_Name_Confine, $field))
                    $this->_Trim = ",trim({$field}{$_str}))";
            }
        }
        return $this->__getSQL();
    }
    /**
     * 去除右边指定字符（空格）
     * @var string $_R_Trim;
     */
    protected $_R_Trim = null;
    /**
     * 去除右边指定字符（空格）
     * @access public
     * @param mixed $field
     * @param string $str
     * @return object
     */
    protected function rTrim($field,$str=null)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_str = null;
                switch ($this->_Data_Type){
                    case "pgsql":
                    case "sqlite":
                    case "oracle":
                        if(!is_null($field[$_i]["str"]))
                            $_str = ",{$field[$_i]["str"]}";
                        break;
                    case "mssql":
                    case "mariadb":
                    default:
                        null;
                        break;
                }
                if(!key_exists("as_name",$field[$_i])){
                    $this->_R_Trim = ",rtrim({$field[$_i]["field"]}{$_str})";
                }else{
                    $this->_R_Trim = ",rtrim({$field[$_i]["field"]}{$_str}) as ".$field[$_i]["as_name"];
                }
            }
        }else{
            $_str = null;
            switch ($this->_Data_Type){
                case "pgsql":
                case "sqlite":
                case "oracle":
                    if(!is_null($str))
                        $_str = ",{$str}";
                    break;
                case "mssql":
                case "mariadb":
                default:
                    null;
                    break;
            }
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_R_Trim = ",rtrim({$field}{$_str}))";
        }
        return $this->__getSQL();
    }
    /**
     * 指定字符替换
     * @var string $_Replace
    */
    protected $_Replace = null;
    /**
     * 指定字符替换
     * @access public
     * @param mixed $field
     * @param string $pattern
     * @param string $replace
     * @return object
    */
    function replace($field,$pattern=null,$replace=null)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                if(!key_exists("as_name",$field[$_i])){
                    $this->_LowerCase .= ",replace({$field[$_i]["field"]},{$field[$_i]["pattern"]},{$field[$_i]["replace"]})";
                }else{
                    $this->_LowerCase .= ",replace({$field[$_i]["field"]},{$field[$_i]["pattern"]},{$field[$_i]["replace"]}) as ".$field[$_i]["as_name"];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Replace = ",replace({$field},{$pattern},{$replace})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Uppercase
     * 需返回信息中所有字母大写的字段名，返回值为数组
     */
    protected $_UpperCase = null;
    /**
     * 返回指定字段信息中的字母全部大写，支持数组及字符串
     * 当含有多个字段名，使用数组，单个字段使用字符串
     * @access public
     * @param mixed $field
     * @return object
     */
    function upper($field)
    {
        /**
         * 区别数据类型使用SQL命名规则对输入的字段名进行验证
         */
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                if(is_numeric(array_keys($field)[0])){
                    $this->_LowerCase .= ",upper({$field[$_i]})";
                }else{
                    $this->_LowerCase .= ",upper({".array_keys($field)[$_i]."}) as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_UpperCase = ",upper({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Lowercase
     * 需返回信息中所有字母小写的字段名，返回值为数组
     */
    protected $_LowerCase = null;
    /**
     * 返回指定字段信息中的字母全部小写，支持数组及字符串
     * 当含有多个字段名，使用数组，单个字段使用字符串
     * @access public
     * @param mixed $field
     * @return object
     */
    function lower($field)
    {
        /**
         * 区别数据类型使用SQL命名规则对输入的字段名进行验证
         */
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            for($_i=0;$_i<count($field);$_i++){
                if(is_numeric(array_keys($field)[0])){
                    $this->_LowerCase = ",lower({$field[$_i]})";
                }else{
                    $this->_LowerCase = ",lower({".array_keys($field)[$_i]."}) as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_LowerCase = ",lower({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Mid
     * 返回指定字段截取字符特定长度的信息，数组类型
     */
    protected $_Mid = null;
    /**
     * 查询语句对指定字段进行截取
     * @access public
     * @param string|array $field
     * @param int $start
     * @param int $length
     * @return object
    */
    function mid($field, $start=0, $length=0)
    {
        switch($this->_Data_Type){
            case "mysql":
            case "mariadb":
                # 判断数据类型
                if(is_array($field)){
                    # 变量数组信息
                    foreach($field as $_key => $_value){
                        # 判断数组传入结构是否与程序要求相同
                        if(is_array($_value) and array_key_exists('start', $_value) and array_key_exists('length', $_value)){
                            $_as = null;
                            if(array_key_exists('as', $_value)) $_as = ' as '.$_value['as'];
                            # 判断字段名是否符合命名规则
                            if(is_true($this->_Regular_Name_Confine, $_key)){
                                if($_value['length'] > 0){
                                    $this->_Mid .= ', mid(' . $_key . ','.intval($_value['start']).','.intval($_value['length']).')'.$_as;
                                }else{
                                    $this->_Mid .= ', mid(' . $_key . ','.intval($_value['start']).')'.$_as;
                                }
                            }
                        }
                    }
                }else {
                    # 当传入值为字符串结构，判断字段名是否符合命名规则
                    if (is_true($this->_Regular_Name_Confine, $field) and $start >= 0){
                        if ($length > 0){
                            $this->_Mid = ', mid(' . $field . ','.intval($start).','.intval($length).')';
                        }else {
                            $this->_Mid = ', mid(' . $field . ',' . intval($start) . ')';
                        }
                    }
                }
                break;
            default:
                break;
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Length
     * 计算指定字段记录值长度的字段名,同时支持字符串和数组类型
     */
    protected $_Length = null;
    /**
     * 计算指定字段列记录值长度，一般只应用于文本格式信息
     * 方法支持两种数据类型，如果只对一个字段进行操作，使用字符串类型
     * 对多个字段进行操作，则使用自然数标记数组
     * @access public
     * @param string $field
     * @return object
     */
    function length($field)
    {
        switch($this->_Data_Type){
            case "mssql":
                $_func = "len";
                break;
            default:
                $_func = "length";
                break;
        }
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            for($_i=0;$_i<count($field);$_i++){
                if(is_numeric(array_keys($field)[0])){
                    $this->_Length .= ",{$_func}({$field[$_i]})";
                }else{
                    $this->_Length .= ",{$_func}(".array_keys($field)[$_i].") as ".$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field))
                $this->_Length = ",{$_func}({$field})";
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Round
     * 需进行指定小数点长度的四舍五入计算的字段名及截取长度数组
     */
    protected $_Round = null;
    /**
     * 对指定字段进行限定小数长度的四舍五入运算
     * 参数同时支持
     * @access public
     * @param mixed $field
     * @param int $decimals 取舍精度
     * @param int $accuracy 截断精度 mssql支持语法参数项
     * @return object
    */
    function round($field, $decimals = 0,$accuracy=0)
    {
        # 判断数据类型
        if(is_array($field)){
            # 变量数组信息
            foreach($field as $_key => $_value){
                # 判断数组传入结构是否与程序要求相同
                if(is_array($_value) and array_key_exists('decimals', $_value)){
                    $_as = null;
                    if(array_key_exists('as', $_value)) $_as = ' as '.$_value['as'];
                    # 判断字段名是否符合命名规则
                    if(is_true($this->_Regular_Name_Confine, $_key)){
                        $_decimals = ",".intval($_value['field']);
                        $_accuracy = null;
                        if($this->_Data_Type == "mssql")
                            $_accuracy = ",".intval($_value['decimals']);
                        $this->_Round .= ",round({$_key}{$_decimals}{$_accuracy})".$_as;
                    }
                }
            }
        }else {
            # 当传入值为字符串结构，判断字段名是否符合命名规则
            if (is_true($this->_Regular_Name_Confine, $field)){
                $_decimals = ",".intval($decimals);
                $_accuracy = null;
                if($this->_Data_Type == "mssql")
                    $_accuracy = ",".intval($accuracy);
                $this->_Round = ",round({$field}{$_decimals}{$_accuracy})";
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Now
     * 获取数据库当前时间
     */
    protected $_Now = null;
    /**
     * 返回当前数据库时间
     */
    function now()
    {
        $this->_Now = ', nowTime';
        return $this->__getSQL();
    }
    /**
     * @var string $_Format
     * 需进行格式化的记录的字段名及格式信息数组
     */
    protected $_Format = null;
    /**
     * 对指定字段记录进行格式化处理
     * @access public
     * @param mixed $field
     * @param string $format
     * @return object
    */
    function format($field, $format = null)
    {
        switch ($this->_Data_Type){
            case "mysql":
            case "mariadb":
                # 创建验证正则
                $_regular = '/^[^\<\>]+$/';
                # 判断数据类型
                if(is_array($field)){
                    # 变量数组信息
                    foreach($field as $_key => $_value){
                        # 判断数组传入结构是否与程序要求相同
                        if(is_array($_value) and array_key_exists('format', $_value)){
                            $_as = null;
                            if(array_key_exists('as', $_value)) $_as = ' as '.$_value['as'];
                            # 判断字段名是否符合命名规则
                            if(is_true($this->_Regular_Name_Confine, $_key)){
                                $this->_Format .= ",format({$_key},{$_value["format"]}){$_as}";
                            }
                        }
                    }
                }else {
                    # 当传入值为字符串结构，判断字段名是否符合命名规则
                    if (is_true($this->_Regular_Name_Confine, $field) and is_true($_regular, $format))
                        $this->_Format = ",format({$field},{$format})";
                }
                break;
            default:
                break;
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Having
     * 函数应用表达式
     */
    protected $_Having = null;
    /**
     * 函数结构应用, 单项内容操作
     * @access public
     * @param string $func
     * @param string $field
     * @param string $symbol
     * @param int $value
     * @return object
    */
    function having($func, $field, $symbol, $value)
    {
        /**
         * 因为having运算主要用于范围所以当前版本仅支持对数字运算
         * @var string $_regular_function_confine
         * @var string $_regular_symbol_confine
        */
        # 创建可调用函数正则
        $_regular_function_confine = '/^(avg|sum|max|min|len)$/';
        # 创建运算符匹配正则
        $_regular_symbol_confine = '/^(gt|lt|eq|ge|le|neq)$/';
        # 判断参数是否符合预限定结果
        if(is_true($_regular_function_confine, $func)){
            if(is_true($this->_Regular_Name_Confine, $field)){
                if(is_true($_regular_symbol_confine, $symbol)){
                    $_symbol = array('gt' => '>', 'lt' => '<', 'et'=> '=', 'eq' => '==','neq' => '!=', 'ge' => '>=', 'le' => '<=','heq' => '===', 'nheq' => '!==');
                    if(array_key_exists(trim(strtolower($symbol)), $_symbol))
                        $_symbol = $_symbol[trim(strtolower($symbol))];
                    if(is_numeric($value)){
                        # 创建having信息数组
                        $this->_Having = " having {$func}({$field}) {$_symbol} {$value}";
                    }
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Order
     * 排序,与where功能支持相似
     */
    protected $_Order = null;
    /**
     * 查询语句排序条件
     * @access public
     * @param string $field
     * @param string $type
     * @return object
    */
    function order($field, $type='asc')
    {
        /**
         * 使用字符串作为唯一数据类型，通过对参数进行验证，判断参数数据结构
         * @var string $_regular_order
         * @var string $_regular_order_confine
        */
        # 创建order结构正则变量
        $_regular_order = '/^([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`|[^\_\W]+\(([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\))\s(asc|desc)((\,\s?[^\_\W]+(\_[^\_\W]+)*|\,\`.+[^\s]+\`|\,\s?[^\_\W]+\(([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\))\s(asc|desc))*$/';
        # 创建排序参数变量
        $_regular_order_confine = '/^(asc|desc)$/';
        # 判断排序信息
        if(is_array($field)){
            $_i = 0;
            foreach($field as $_key => $_type){
                if($_i == 0)
                    $this->_Order .= " order by {$_key} {$_type}";
                else
                    $this->_Order .= ",{$_key} {$_type}";
                $_i++;
            }

        }else{
            if(is_true($_regular_order, $field))
                $this->_Order = ' order by '.$field;
            else{
                if(is_true($this->_Regular_Name_Confine, $field)){
                    if(is_true($_regular_order_confine, $type)){
                        $this->_Order = " order by {$field} {$type}";
                    }
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_Limit
     * 查询界限值，int或者带两组数字的字符串
     */
    protected $_Limit = null;
    /**
     * 查询语句查询限制，有两个参数构成，起始位置，显示长度
     * @access public
     * @param int $start
     * @param int $length
     * @return object
    */
    function limit($start, $length=0)
    {
        if(is_int($start) and $start >= 0){
            if(is_int($length) and $length > 0){
                switch($this->_Data_Type){
                    case "pgsql":
                    case "sqlite":
                        $this->_Limit = " limit {$length} offset {$start}";
                        break;
                    case "mssql": # mssql不支持limit语法
                        null;
                        break;
                    case "oracle":
                        $this->_Limit = " rownum <= {$start}";
                        break;
                    case "mariadb":
                    default:
                        $this->_Limit = " limit {$start},{$length}";
                        break;
                }
            }else{
                switch($this->_Data_Type){
                    case "oracle":
                        if($start > 0)
                            $this->_Limit = " rownum <= {$start}";
                        break;
                    case "mssql": # mssql不支持limit语法
                        break;
                    default:
                        if($start > 0)
                            $this->_Limit = " limit {$start}";
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * @var string $_fetch_type
     * 查询输出类型，包含3种基本参数，all：完整结构模式，nv：自然数结构模式，kv：字典结构模式
     */
    protected $_Fetch_Type = 'all';
    /**
     * 加载列表显示结构限制
     * @access public
     * @param mixed $fetch_type
     * @return object
    */
    function fetch($fetch_type)
    {
        if(in_array(strtolower(trim($fetch_type)),array('all','nv','kv'))){
            $this->_Fetch_Type = strtolower(trim($fetch_type));
        }else{
            if(intval($fetch_type) < 3){
                $this->_Fetch_Type = array('all','nv','kv')[intval($fetch_type)];
            }
        }
        return $this->__getSQL();
    }
    /**
     * 查询总数结构方法，返回一个整数结果
    */
    abstract function count();
    /**
     * 查询表格信息方法，并返回数组结果集
    */
    abstract function select();
    /**
     * 向表插入信息方法，并返回插入成功后插入后数据id
    */
    abstract function insert();
    /**
     * 删除指定数据记录，并返回执行结果信息
    */
    abstract function delete();
    /**
     * 修改指定数据记录，并返回执行结果信息
     */
    abstract function update();
    /**
     * 执行自定义查询语句,并返回执行结果
     * @param string $query
    */
    abstract function query($query);
    /**
     * 返回错误信息
     * @return string
    */
    function getErrorMsg()
    {
        return $this->_Err_Msg;
    }
}