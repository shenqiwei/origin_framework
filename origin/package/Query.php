<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架Sql操作封装类
 */
namespace Origin\Package;

use Exception;

/**
 * 封装类，数据库操作，主结构访问类
 */
abstract class Query
{
    /**
     * 操作常量
     * @access public
     */
    const FETCH_NORMAL = "normal";
    const FETCH_KEY_VALUE = "key_value";
    const FETCH_NUMBER_VALUE = "integer_value";
    const RESOURCE_TYPE_MYSQL = 0;
    const RESOURCE_TYPE_PGSQL = 1;
    const RESOURCE_TYPE_MSSQL = 2;
    const RESOURCE_TYPE_SQLITE = 3;
    const RESOURCE_TYPE_ORACLE = 4;
    const RESOURCE_TYPE_MARIADB = 5;

    /**
     * @access protected
     * @var string $NameConfine SQL基础验证正则表达式变量
     */
    protected $NameConfine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)$/';

    /**
     * @access protected
     * @var string $CommaConfine SQL基础验证正则表达式变量
     */
    protected $CommaConfine = '/^([^\_\W]+(\_[^\_\W]+)*(\.?[^\_\W]+(\_[^\_\W]+)*)*|\`.+[^\s]+\`)(\,\s?[^\_\W]+(\_[^\_\W]+)*|\,\`.+[^\s]+\`)*$/';

    /**
     * @access protected
     * @var object $Object 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $Object = null;

    /**
     * @access protected
     * @var string $ErrMsg 数据库错误信息变量
     */
    protected $ErrMsg = null;

    /**
     * @access protected
     * @var int $DataType 数据源类型
     */
    protected $DataType = 0;

    /**
     * 回传类对象信息
     * @access public
     * @param object $object
     * @return void
     */
    function __setSQL(object $object)
    {
        $this->Object = $object;
    }

    /**
     * 获取类对象信息,仅类及其子类能够使用
     * @access public
     * @return object 返回数据库连接对象
     */
    protected function __getSQL(): object
    {
        return $this->Object;
    }

    /**
     * @access protected
     * @var string $Primary 自增主键字段
    */
    protected $Primary = null;

    /**
     * 设置自增主键字段名信息
     * @access public
     * @param string $field 主键名称
     * @return object 返回数据库连接对象
     */
    function setPrimary(string $field): object
    {
        if(is_true($this->NameConfine, $field))
            $this->Primary = $field;
        return $this->Object;
    }

    /**
     * @access protected
     * @var string $Table 数据库表名，表名与映射结构及Model结构可以同时使用，当使用映射结构和model结构时，表名只做辅助
     */
    protected $Table = null;
    protected $AsTable = null;

    /**
     * 表名获取方法
     * @access public
     * @param string $table 表名
     * @param string|null $table_as 表别名
     * @return object 返回数据库连接对象
     */
    function table(string $table, ?string $table_as=null): object
    {
        # 初始化所有参与项
        $this->Table = null; # 主表名
        $this->AsTable = null; # 主表别名
        $this->JoinOn = null; # 关联结构式
        $this->Top = null; # sql语法top
        $this->Total = null; # count 语法结构式
        $this->Field = "*"; # field语法结构式
        $this->Distinct = null; # 不重复结构式
        $this->Union = null; # 相同合并结构式
        $this->Data = array(); # 提交数据结构式用于支持insert和update
        $this->Where = null; # 条件结构式
        $this->Group = null; # 分组结构式
        $this->Abs = null; # 求正整数
        $this->Avg = null; # 求平均数
        $this->Max = null; # 求最大值
        $this->Min = null; # 求最小值
        $this->Sum = null; # 求数值综合综合
        $this->Mod = null; # 取余结构式
        $this->Random = null; # 随机数结构式
        $this->LTrim = null; # 去左空格或指定符号内容
        $this->Trim = null; # 去两侧空格或指定符号内容
        $this->RTrim = null; # 去右空格或指定符号内容
        $this->Replace = null; # 替换指定符号内容
        $this->UpperCase = null; # 全大写
        $this->LowerCase = null; # 全小写
        $this->Mid = null; # 截取字段
        $this->Length = null; # 求字符长度
        $this->Round = null; # 四舍五入
        $this->Now = null; # 当前服务器时间
        $this->Format = null; # 格式化显示数据
        $this->Having = null; # 类似where可控语法函数
        $this->Order = null; # 排序
        $this->Limit = null; # 显示范围
        $this->FetchType = "all"; # 显示类型
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        if(is_true($this->CommaConfine, $table)){
            $this->Table = strtolower($table);
            if(!is_null($table_as) and is_true($this->CommaConfine, $table_as))
                $this->AsTable = "as ".$table_as;
        }else{
            try{
                throw new Exception('Table name is not in conformity with the naming conventions');
            }catch(Exception $e){
                exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $JoinOn 多表联合匹配条件
     */
    protected $JoinOn = null;

    /**
     * 多表关系匹配 join 语句，支持多表联查，根据join特性join后接表名为单表
     * 多表联合匹配条件 on，与join联合使用，当field只有一个值时，系统会自动调用表格中，同名字段名
     * 当有多个条件时，可以使用数组进行结构导入
     * @access public
     * @param string $join_table 关联表名
     * @param string $join_field 关联表外键名
     * @param string $major_field 主表关联建名
     * @param string|null $join_table_as 关联表别名
     * @param string|null $join_type 关联类型
     * @return object 返回数据库连接对象
     */
    function join(string $join_table, string $join_field, string $major_field, ?string $join_table_as=null, ?string $join_type=null): object
    {
        if(in_array(strtolower(trim($join_type)),array("inner","left","right")))
            $join_type = strtolower(trim($join_type))." ";
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        if (is_true($this->CommaConfine, $join_table)) {
            # 根据SQL数据库命名规则判断字段名是否符合规则要求，如果符合装在进SQL模块Field变量中
            if (is_true($this->CommaConfine, $join_field)) {
                if (is_true($this->CommaConfine, $major_field)) {
                    if(is_null($join_table_as)){
                        if(is_null($this->AsTable))
                            $this->JoinOn .= " $join_type join $join_table on $join_table.$join_field = $this->Table.$major_field";
                        else
                            $this->JoinOn .= " $join_type join $join_table on $join_table.$join_field = $this->AsTable.$major_field";
                    }else{
                        if(is_null($this->AsTable))
                            $this->JoinOn .= " $join_type join $join_table as $join_table_as on $join_table_as.$join_field = $this->Table.$major_field";
                        else
                            $this->JoinOn .= " $join_type join $join_table as $join_table_as on $join_table_as.$join_field = $this->AsTable.$major_field";
                    }
                } else {
                    # 异常处理：字段名称不符合命名规范
                    try {
                        throw new Exception('Field name is not in conformity with the naming conventions');
                    } catch (Exception $e) {
                        exception("Query Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            } else {
                # 异常处理：字段名称不符合命名规范
                try {
                    throw new Exception('Field name is not in conformity with the naming conventions');
                } catch (Exception $e) {
                    exception("Query Error", $e->getMessage(), debug_backtrace(0, 1));
                    exit();
                }
            }
        } else {
            try {
                throw new Exception('Join Table name is not in conformity with the naming conventions');
            } catch (Exception $e) {
                exception("Query Error", $e->getMessage(), debug_backtrace(0, 1));
                exit();
            }
        }
        return $this->__getSQL();
    }

    /**
     * 多表关系匹配 inner join 语句，利用join语法演化方法
     * @access public
     * @param string $join_table 关联表名
     * @param string $join_field 关联表外键名
     * @param string $major_field 主表关联建名
     * @param string|null $join_table_as 关联表别名
     * @return object 返回数据库连接对象
     */
    function iJoin(string $join_table, string $join_field, string $major_field, ?string $join_table_as=null): object
    {
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        $this->join($join_table,$join_field,$major_field,$join_table_as,"inner");
        return $this->__getSQL();
    }

    /**
     * 多表关系匹配 left join 语句，利用join语法演化方法
     * @access public
     * @param string $join_table 关联表名
     * @param string $join_field 关联表外键名
     * @param string $major_field 主表关联建名
     * @param string|null $join_table_as 关联表别名
     * @return object 返回数据库连接对象
     */
    function lJoin(string $join_table, string $join_field, string $major_field, ?string $join_table_as=null): object
    {
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        $this->join($join_table,$join_field,$major_field,$join_table_as,"left");
        return $this->__getSQL();
    }

    /**
     * 多表关系匹配 right join 语句，利用join语法演化方法
     * @access public
     * @param string $join_table 关联表名
     * @param string $join_field 关联表外键名
     * @param string $major_field 主表关联建名
     * @param string|null $join_table_as 关联表别名
     * @return object 返回数据库连接对象
     */
    function rJoin(string $join_table, string $join_field, string $major_field, ?string $join_table_as=null): object
    {
        # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
        $this->join($join_table,$join_field,$major_field,$join_table_as,"right");
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Top 用于select查询中Top应用
     */
    protected $Top = null;

    /**
     * Top 语句结构
     * @access public
     * @param int $number 显示数量
     * @param boolean $percent 是否启用比例显示
     * @return object 返回数据库连接对象
     */
    function top(int $number, bool $percent=false): object
    {
        switch($this->DataType){
            case self::RESOURCE_TYPE_MSSQL:
                # top 关键字后边只能接数组，所以在拼接语句时，要对number进行类型转化
                $this->Top .=' top '.$number;
                # 判断是否使用百分比进行查询
                if($percent and $number > 100)
                    $this->Top = ' top 100 percent';
                elseif($percent and $number <= 100)
                    $this->Top .= ' percent';
                break;
            default:
                break;
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Total 用于select查询中求总数，语句结构利用count函数
     */
    protected $Total = null;

    /**
     * 返回指定数据表数据总数
     * @access public
     * @param string $field 字段名
     * @return object 返回数据库连接对象
     */
    function total(string $field='*'): object
    {
        if(is_true($this->NameConfine, $field))
            $this->Total = "count($field)";
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Field 查询元素，用于在select查询中精确查寻数据, 支持数组格式，同时支持as关键字
     */
    protected $Field = '*';

    /**
     * 查询字段名，默认信息是符号（*），当传入值是数组时，$key为原字段名，$value为简写名
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function field($field): object
    {
        # 判断field值类型是否为数组(进行传入值结构判断，如果传入值为数组并且数组元素总数大于0)
        if(is_array($field)){
            # 判断数组大小是否大于0
            if(count($field)){
                # 创建计数变量
                $i=0;
                # 使用foreach函数进行数组遍历
                foreach($field as $key => $value){
                    # 判断字段名和简名是否和规
                    if(is_true($this->CommaConfine, $value)) {
                        if(is_true($this->CommaConfine, $key) and !is_numeric($key)){
                            # 判断计数变量当前值，等于0时不添加连接符号
                            if ($i == 0)
                                $this->Field = " $key as $value";
                            else
                                $this->Field .= ",$key as $value";
                        }else{
                            if($i == 0)
                                $this->Field = $value;
                            else
                                $this->Field .= ",$value";
                        }
                        $i += 1;
                    }else{
                        try{
                            throw new Exception('Field name is not in conformity with the naming conventions');
                        }catch(Exception $e){
                            exception("Query Error", $e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }
                }
            }
        }else{
            # 根据SQL数据库命名规则判断字段名是否符合规则要求，如果符合装在进SQL模块Field变量中
            if(is_true($this->CommaConfine, $field))
                $this->Field = $field;
            else{
                # 异常处理：字段名称不符合命名规范
                try{
                    throw new Exception('Field name is not in conformity with the naming conventions');
                }catch(Exception $e){
                    exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Distinct 查询单字段不重复值
     */
    protected $Distinct = null;

    /**
     * 列出单列字段名不同值 distinct 语句，该语句仅支持单列字段显示，如果需要显示多列信息，需要时group
     * 在一些应用场景中，可以把distinct看作是group的简化功能结构
     * @access public
     * @param string $field 字段名
     * @return object 返回数据库连接对象
     */
    function distinct(string $field): object
    {
        # 进行传入值结构判断
        if(is_true($this->NameConfine, $field))
            $this->Distinct = ",distinct $field";
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Union 低效相同列，相同数，支持单个或多个
     */
    protected $Union = null;

    /**
     * 对多个查询语句的相同字段中的相同数据进行合并,当前版本仅支持两个表单字段查询，并对输入结构进行验证和隔离
     * @access public
     * @param string $table 比对表名
     * @param string $field 字段名
     * @return object 返回数据库连接对象
     */
    function union(string $table, string $field): object
    {
        $this->Union = null;
        # 判断传入参数表名和字段名是否符合命名规则,使用SQL命名规则对输入的表名和字段名进行验证
        if(is_true($this->NameConfine, $table) and is_true($this->NameConfine, $field))
            $this->Union = " union select $field from $table";
        else{
            if(is_true($this->NameConfine, $table)){
                try{
                    throw new Exception('The field name is not in conformity with the SQL naming rules');
                }catch(Exception $e){
                    exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }else{
                # 异常处理：表名名不符合SQL命名规则
                try{
                    throw new Exception('The table name is not in conformity with the SQL naming rules');
                }catch(Exception $e){
                    exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var array $Data 用户存储需要修改或者添加的数据信息，该模块与验证模块连接使用
     */
    protected $Data = array();

    /**
     * 添加修改值获取方法,传入值结构为数组，数组key为字段名，数组value为传入值
     * @access public
     * @param array $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function data(array $field): object
    {
        # 验证传入值结构，符合数组要求时，进行内容验证
        # 判断传入值是否为数组
        if($field){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            foreach($field as $key => $value){
                if(is_true($this->NameConfine, $key)){
                    if(!is_array($this->Data)) $this->Data = array();
                    array_push($this->Data, array($key => $value));
                    # this->_Data[$key] = $value;
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(Exception $e){
                        exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
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
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Where sql语句条件变量，分别为两种数据类型，当为字符串时，直接引用，当为数组时，转化执行
     */
    protected $Where = null;

    /**
     * 条件信息加载方法，传入值类型支持字符串、数组，数组结构可以为多级数组
     * 1.当数组key为字段名，数组value为条件值，条件表述为等于条件，若数组value为数组结构
     * 2.当数组key为特定字符串（$and 或 $or）,数组value必须为数组结构，数组结构表述与表述 1要求相同
     * 3.数组关系结构中，同级条件结构放在同一个上级数组内容
     * 3.当为字符串，要求条件信息符合SQL语句规则
     * @access public
     * @param mixed $condition 条件参数，可以是条件内容（string）也可以条件集合（array）
     * @return object 返回数据库连接对象
     */
    function where($condition): object
    {
        # 区别数据类型使用SQL命名规则对输入的字段名进行验证
        if(is_array($condition))
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            $this->Where = " where ".$this->multiWhere($condition);
        else{
            # 对输入字符串进行特殊字符转义，降低XSS攻击
            # 用预设逻辑语法数组替代特殊运算符号
            if(!empty($condition)){
                foreach(array('/\s+gt\s+/' => '>', '/\s+lt\s+/ ' => '<','/\s+neq\s+/' => '!=', '/\s+eq\s+/'=> '=',
                            '/\s+ge\s+/' => '>=', '/\s+le\s+/' => '<=','/\s+in\s+/'=>' in ','/\s+nin\s+/'=>" not in ") as $key => $value)
                    $condition = preg_replace($key, $value, $condition);
                $this->Where = " where $condition";
            }
        }
        return $this->__getSQL();
    }

    /**
     * 条件拆分函数
     * @access private
     * @param array $where
     * @return string 返回语句运算符号
    */
    private function multiWhere(array $where)
    {
        $is_multi = false;
        if(count($where) > 1) $is_multi = true;
        foreach($where as $key => $value) {
            if ($key == "\$and") {
                if ($is_multi)
                    $where .= " and (" . $this->multiWhere($value) . ")";
                else
                    $where .= " and " . $this->multiWhere($value);
            } elseif ($key == "\$or") {
                if ($is_multi)
                    $where .= " or (" . $this->multiWhere($value) . ")";
                else
                    $where .= " or " . $this->multiWhere($value);
            } elseif (is_true($this->NameConfine, $key)) {
                if (is_array($value)) {
                    $first_key = array_keys($value)[0];
                    $symbol = "=";
                    switch ($first_key) {
                        case "\$eq":
                            $symbol = "=";
                            break;
                        case "\$lt":
                            $symbol = "<";
                            break;
                        case "\$gt":
                            $symbol = ">";
                            break;
                        case "\$in":
                            $symbol = "in";
                            break;
                        case "\$le":
                            $symbol = "<=";
                            break;
                        case "\$ge":
                            $symbol = ">=";
                            break;
                        case "\$neq":
                            $symbol = "!=";
                            break;
                        case "\$nin":
                            $symbol = "not in";
                            break;
                    }
                    if (is_null($where)) {
                        if (is_integer($value) or is_float($value) or is_double($value)) {
                            $where = " $key $symbol $value";
                        } else {
                            $where = " $key $symbol '$value'";
                        }
                    } else {
                        if (is_int($value) or is_float($value) or is_double($value)) {
                            $where .= " and $key $symbol $value";
                        } else {
                            $where .= " and $key $symbol '$value'";
                        }
                    }
                } else {
                    # 将数组信息存入类变量
                    if (is_null($where)) {
                        if (is_integer($value) or is_float($value) or is_double($value)) {
                            $where = " $key = $value";
                        } else {
                            $where = " $key = '$value'";
                        }
                    } else {
                        if (is_int($value) or is_float($value) or is_double($value)) {
                            $where .= " and $key = $value";
                        } else {
                            $where .= " and $key = '$value'";
                        }
                    }
                }
            }else{
                # 异常处理：字段名不符合SQL命名规则
                try{
                    throw new Exception('The field name is not in conformity with the SQL naming rules');
                }catch(Exception $e){
                    exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $where;
    }

    /**
     * @access protected
     * @var string $Group 分组变量，与where功能支持相似
     */
    protected $Group = null;

    /**
     * 去重（指定字段名）显示列表信息
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function group($field): object
    {
        # 判断传入参数类型，区别数据类型使用SQL命名规则对输入的字段名进行验证
        if(is_array($field)){
            # 创建编辑变量
            $i = 0;
            # 循环遍历数组内元素信息
            foreach($field as $value){
                # 验证元素信息值是否符合SQL命名规则
                if(is_true($this->NameConfine, $value)){
                    # 拼接条件信息
                    if($i == 0)
                        $this->Group = " group by $value";
                    else
                        $this->Group .= ",$value";
                    $i++;
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(Exception $e){
                        exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }
        }else{
            # 使用多条件结构正则验证字符串内容
            if(is_true($this->CommaConfine, $field))
                $this->Group = " group by $field";
            else{
                # 异常处理：GROUP语法字段名结构不符合SQL使用规则
                try{
                    throw new Exception('Group of grammatical structure of the field name is not in
                                               conformity with the SQL using rules');
                }catch(Exception $e){
                    exception("Query Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Abs 求正整数
    */
    protected $Abs = null;

    /**
     * 求正整数值
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
    */
    function abs($field)
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                $symbol = '';
                if($i!=0) $symbol = ',';
                if(is_numeric(array_keys($field)[0]))
                    $this->Abs .= "{$symbol}abs($field[$i])";
                else
                    $this->Abs .= "{$symbol}abs(".array_keys($field)[$i].") as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->Abs = "abs($field)";
        }
        return $this->Object;
    }

    /**
     * @access protected
     * @var string $Avg 求平均数函数的字段名
     */
    protected $Avg = null;

    /**
     * 查询语句指定字段值平均数，支持单字段名
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function avg($field): object
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                $symbol = '';
                if($i!=0) $symbol = ',';
                if(is_numeric(array_keys($field)[0]))
                    $this->Avg .= "{$symbol}avg($field[$i])";
                else
                    $this->Avg .= "{$symbol}avg(".array_keys($field)[$i].") as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->Avg = "avg($field)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Max 指定字段下最大记录值的字段名
     */
    protected $Max = null;

    /**
     * 查询语句指定字段中最大值，支持单字段名
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function max($field)
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                $symbol = '';
                if($i!=0) $symbol = ',';
                if(is_numeric(array_keys($field)[0]))
                    $this->Max .= "{$symbol}max($field[$i])";
                else
                    $this->Max .= "{$symbol}max(".array_keys($field)[$i].") as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->Max = "max($field)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Min 指定字段下最小记录值的字段名
     */
    protected $Min = null;

    /**
     * 查询语句指定字段中最小值，支持单字段名
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function min($field)
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                $symbol = '';
                if($i!=0) $symbol = ',';
                if(is_numeric(array_keys($field)[0]))
                    $this->Min .= "{$symbol}min($field[$i])";
                else
                    $this->Min .= "{$symbol}min(".array_keys($field)[$i].") as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->Min = "min($field)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Sum 计算字段下所有列数值总和的字段名
     */
    protected $Sum = null;

    /**
     * 返回指定字段下所有列值的总和，只支持数字型字段列
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function sum($field)
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                $symbol = '';
                if($i!=0) $symbol = ',';
                if(is_numeric(array_keys($field)[0]))
                    $this->Sum .= "{$symbol}sum($field[$i])";
                else
                    $this->Sum .= "{$symbol}sum(".array_keys($field)[$i].") as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->Sum = "sum($field)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Mod 取余
    */
    protected $Mod = null;

    /**
     * 取余
     * @access public
     * @param mixed $field 字段名（列表list）
     * @param int $second 精度
     * @return object 返回数据库连接对象
    */
    function mod($field, int $second=0): object
    {
        switch($this->DataType){
            case self::RESOURCE_TYPE_SQLITE:
                break;
            case self::RESOURCE_TYPE_MSSQL:
            case self::RESOURCE_TYPE_MARIADB:
            case self::RESOURCE_TYPE_ORACLE:
            case self::RESOURCE_TYPE_PGSQL:
            default:
                if(is_array($field)){
                    for($i=0;$i<count($field);$i++){
                        if(!key_exists("as_name",$field[$i]))
                            $this->Mod = ",mod({$field[$i]["first"]},{$field[$i]["second"]})";
                        else
                            $this->Mod = ",mod({$field[$i]["first"]},{$field[$i]["second"]}}) as {$field[$i]["as_name"]}";
                    }
                }else{
                    if(is_true($this->NameConfine, $field))
                        $this->Mod = ", mod($field,$second)";
                }
                break;
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Random 求随机数
    */
    protected $Random = null;

    /**
     * 求随机数
     * @access public
     * @return object 返回数据库连接对象
    */
    function random(): ?object
    {
        switch ($this->DataType){
            case self::RESOURCE_TYPE_PGSQL:
            case self::RESOURCE_TYPE_SQLITE:
                $this->Random = ",random()";
                break;
            case self::RESOURCE_TYPE_ORACLE:
                $this->Random = ",dbms_random.value";
                break;
            case self::RESOURCE_TYPE_MSSQL:
            case self::RESOURCE_TYPE_MARIADB:
            default:
                $this->Random = ",rand()";
                break;
        }
        return $this->Object;
    }

    /**
     * @access protected
     * @var string $LTrim 去除左边指定字符（空格）
    */
    protected $LTrim = null;

    /**
     * 去除左边指定字符（空格）
     * @access public
     * @param mixed $field 字段名（列表list）
     * @param string|null $str 消除符号
     * @return object 返回数据库连接对象
    */
    function lTrim($field, ?string $str=null): object
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                switch ($this->DataType){
                    case self::RESOURCE_TYPE_PGSQL:
                    case self::RESOURCE_TYPE_SQLITE:
                    case self::RESOURCE_TYPE_ORACLE:
                        if(!is_null($field[$i]["str"]))
                            $str = ",{$field[$i]["str"]}";
                        break;
                    case self::RESOURCE_TYPE_MSSQL:
                    case self::RESOURCE_TYPE_MARIADB:
                    default:
                        break;
                }
                if(!key_exists("as_name",$field[$i]))
                    $this->LTrim = ",ltrim({$field[$i]["field"]}$str)";
                else
                    $this->LTrim = ",ltrim({$field[$i]["field"]}$str) as ".$field[$i]["as_name"];
            }
        }else{
            switch ($this->DataType){
                case self::RESOURCE_TYPE_PGSQL:
                case self::RESOURCE_TYPE_SQLITE:
                case self::RESOURCE_TYPE_ORACLE:
                    if(!is_null($str))
                        $str = ",$str";
                    break;
                case self::RESOURCE_TYPE_MSSQL:
                case self::RESOURCE_TYPE_MARIADB:
                default:
                    break;
            }
            if(is_true($this->NameConfine, $field))
                $this->LTrim = ",ltrim($field$str))";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Trim 去除指定字符（空格）
     */
    protected $Trim = null;

    /**
     * 去除指定字符（空格）
     * @access public
     * @param mixed $field 字段名（列表list）
     * @param string|null $str 消除符号
     * @return object 返回数据库连接对象
     */
    function trim($field, ?string $str=null): object
    {
        if($this->DataType != self::RESOURCE_TYPE_MSSQL){
            if(is_array($field)){
                for($i=0;$i<count($field);$i++){
                    switch ($this->DataType){
                        case self::RESOURCE_TYPE_PGSQL:
                        case self::RESOURCE_TYPE_SQLITE:
                        case self::RESOURCE_TYPE_ORACLE:
                            if(!is_null($field[$i]["str"]))
                                $str = ",{$field[$i]["str"]}";
                            break;
                        case self::RESOURCE_TYPE_MARIADB:
                        default:
                            break;
                    }
                    if(!key_exists("as_name",$field[$i]))
                        $this->Trim = ",trim({$field[$i]["field"]}$str)";
                    else
                        $this->Trim = ",trim({$field[$i]["field"]}$str) as ".$field[$i]["as_name"];
                }
            }else{
                switch ($this->DataType){
                    case self::RESOURCE_TYPE_PGSQL:
                    case self::RESOURCE_TYPE_SQLITE:
                    case self::RESOURCE_TYPE_ORACLE:
                        if(!is_null($str))
                            $str = ",$str";
                        break;
                    case self::RESOURCE_TYPE_MARIADB:
                    default:
                        break;
                }
                if(is_true($this->NameConfine, $field))
                    $this->Trim = ",trim($field$str))";
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $RTrim 去除右边指定字符（空格）
     */
    protected $RTrim = null;

    /**
     * 去除右边指定字符（空格）
     * @access public
     * @param mixed $field 字段名（列表list）
     * @param string|null $str 消除符号
     * @return object 返回数据库连接对象
     */
    function rTrim($field, ?string $str=null): object
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                switch ($this->DataType){
                    case self::RESOURCE_TYPE_PGSQL:
                    case self::RESOURCE_TYPE_SQLITE:
                    case self::RESOURCE_TYPE_ORACLE:
                        if(!is_null($field[$i]["str"]))
                            $str = ",{$field[$i]["str"]}";
                        break;
                    case self::RESOURCE_TYPE_MSSQL:
                    case self::RESOURCE_TYPE_MARIADB:
                    default:
                        break;
                }
                if(!key_exists("as_name",$field[$i]))
                    $this->RTrim = ",rtrim({$field[$i]["field"]}$str)";
                else
                    $this->RTrim = ",rtrim({$field[$i]["field"]}$str) as ".$field[$i]["as_name"];
            }
        }else{
            $str = null;
            switch ($this->DataType){
                case self::RESOURCE_TYPE_PGSQL:
                case self::RESOURCE_TYPE_SQLITE:
                case self::RESOURCE_TYPE_ORACLE:
                    if(!is_null($str))
                        $str = ",$str";
                    break;
                case self::RESOURCE_TYPE_MSSQL:
                case self::RESOURCE_TYPE_MARIADB:
                default:
                    break;
            }
            if(is_true($this->NameConfine, $field))
                $this->RTrim = ",rtrim($field$str))";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Replace 指定字符替换
    */
    protected $Replace = null;

    /**
     * 指定字符替换
     * @access public
     * @param mixed $field 字段名（列表list）
     * @param string|null $pattern 检索内容
     * @param string|null $replace 替换内容
     * @return object 返回数据库连接对象
    */
    function replace($field, ?string $pattern=null, ?string $replace=null): object
    {
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                if(!key_exists("as_name",$field[$i]))
                    $this->Replace .= ",replace({$field[$i]["field"]},{$field[$i]["pattern"]},{$field[$i]["replace"]})";
                else
                    $this->Replace .= ",replace({$field[$i]["field"]},{$field[$i]["pattern"]},{$field[$i]["replace"]}) as ".$field[$i]["as_name"];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->Replace = ",replace($field,$pattern,$replace)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Uppercase 需返回信息中所有字母大写的字段名，返回值为数组
     */
    protected $UpperCase = null;

    /**
     * 返回指定字段信息中的字母全部大写，支持数组及字符串
     * 当含有多个字段名，使用数组，单个字段使用字符串
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function upper($field): object
    {
        # 区别数据类型使用SQL命名规则对输入的字段名进行验证
        if(is_array($field)){
            for($i=0;$i<count($field);$i++){
                if(is_numeric(array_keys($field)[0]))
                    $this->UpperCase .= ",upper($field[$i])";
                else
                    $this->UpperCase .= ",upper({".array_keys($field)[$i]."}) as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->UpperCase = ",upper($field)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Lowercase 需返回信息中所有字母小写的字段名，返回值为数组
     */
    protected $LowerCase = null;

    /**
     * 返回指定字段信息中的字母全部小写，支持数组及字符串
     * 当含有多个字段名，使用数组，单个字段使用字符串
     * @access public
     * @param mixed $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function lower($field): object
    {
        # 区别数据类型使用SQL命名规则对输入的字段名进行验证
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            for($i=0;$i<count($field);$i++){
                if(is_numeric(array_keys($field)[0]))
                    $this->LowerCase = ",lower($field[$i])";
                else
                    $this->LowerCase = ",lower(".array_keys($field)[$i].") as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->LowerCase = ",lower($field)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Mid 返回指定字段截取字符特定长度的信息，数组类型
     */
    protected $Mid = null;

    /**
     * 查询语句对指定字段进行截取
     * @access public
     * @param string|array $field 字段名（列表list）
     * @param int $start 起始位置
     * @param int $length 截取长度
     * @return object 返回数据库连接对象
    */
    function mid($field, int $start=0, int $length=0): object
    {
        switch($this->DataType){
            case self::RESOURCE_TYPE_MYSQL:
            case self::RESOURCE_TYPE_MARIADB:
                # 判断数据类型
                if(is_array($field)){
                    # 变量数组信息
                    foreach($field as $key => $value){
                        # 判断数组传入结构是否与程序要求相同
                        if(is_array($value) and array_key_exists('start', $value) and array_key_exists('length', $value)){
                            $as = null;
                            if(array_key_exists('as', $value)) $as = ' as '.$value['as'];
                            # 判断字段名是否符合命名规则
                            if(is_true($this->NameConfine, $key)){
                                if($value['length'] > 0)
                                    $this->Mid .= ', mid(' . $key . ','.intval($value['start']).','.intval($value['length']).')'.$as;
                                else
                                    $this->Mid .= ', mid(' . $key . ','.intval($value['start']).')'.$as;
                            }
                        }
                    }
                }else {
                    # 当传入值为字符串结构，判断字段名是否符合命名规则
                    if (is_true($this->NameConfine, $field) and $start >= 0){
                        if($length > 0)
                            $this->Mid = ", mid($field,$start,$length)";
                        else
                            $this->Mid = ", mid($field,$start)";
                    }
                }
                break;
            default:
                break;
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Length 计算指定字段记录值长度的字段名,同时支持字符串和数组类型
     */
    protected $Length = null;

    /**
     * 计算指定字段列记录值长度，一般只应用于文本格式信息
     * 方法支持两种数据类型，如果只对一个字段进行操作，使用字符串类型
     * 对多个字段进行操作，则使用自然数标记数组
     * @access public
     * @param string $field 字段名（列表list）
     * @return object 返回数据库连接对象
     */
    function length(string $field): object
    {
        switch($this->DataType){
            case self::RESOURCE_TYPE_MSSQL:
                $func = "len";
                break;
            default:
                $func = "length";
                break;
        }
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            for($i=0;$i<count($field);$i++){
                if(is_numeric(array_keys($field)[0]))
                    $this->Length .= ",$func($field[$i])";
                else
                    $this->Length .= ",$func(".array_keys($field)[$i].") as ".$field[array_keys($field)[$i]];
            }
        }else{
            if(is_true($this->NameConfine, $field))
                $this->Length = ",$func($field)";
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Round 需进行指定小数点长度的四舍五入计算的字段名及截取长度数组
     */
    protected $Round = null;

    /**
     * 对指定字段进行限定小数长度的四舍五入运算，参数同时支持
     * @access public
     * @param mixed $field 字段名（列表list）
     * @param int $decimals 取舍精度
     * @param int $accuracy 截断精度 mssql支持语法参数项
     * @return object 返回数据库连接对象
    */
    function round($field, int $decimals = 0, int $accuracy=0): object
    {
        # 判断数据类型
        if(is_array($field)){
            # 变量数组信息
            foreach($field as $key => $value){
                # 判断数组传入结构是否与程序要求相同
                if(is_array($value) and array_key_exists('decimals', $value)){
                    $as = null;
                    if(array_key_exists('as', $value)) $as = ' as '.$value['as'];
                    # 判断字段名是否符合命名规则
                    if(is_true($this->NameConfine, $key)){
                        $decimals = ",".intval($value['field']);
                        if($this->DataType == self::RESOURCE_TYPE_MSSQL)
                            $accuracy = ",$accuracy";
                        else
                            $accuracy = null;
                        $this->Round .= ",round($key$decimals$accuracy)".$as;
                    }
                }
            }
        }else {
            # 当传入值为字符串结构，判断字段名是否符合命名规则
            if (is_true($this->NameConfine, $field)){
                $decimals = ",$decimals";
                $accuracy = null;
                if($this->DataType == self::RESOURCE_TYPE_MSSQL)
                    $accuracy = ",".intval($accuracy);
                else
                    $accuracy = null;
                $this->Round = ",round($field$decimals$accuracy)";
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Now 获取数据库当前时间
     */
    protected $Now = null;

    /**
     * 返回当前数据库时间
     * @access public
     * @return object 返回数据库连接对象
     */
    function now(): object
    {
        $this->Now = ', nowTime';
        return $this->__getSQL();
    }

    /**
     * @var string $Format
     * 需进行格式化的记录的字段名及格式信息数组
     */
    protected $Format = null;

    /**
     * 对指定字段记录进行格式化处理
     * @access public
     * @param mixed $field
     * @param string|null $format
     * @return object 返回数据库连接对象
    */
    function format($field, string $format = null): object
    {
        switch ($this->DataType){
            case self::RESOURCE_TYPE_MYSQL:
            case self::RESOURCE_TYPE_MARIADB:
                # 创建验证正则
                $regular = '/^[^\<\>]+$/';
                # 判断数据类型
                if(is_array($field)){
                    # 变量数组信息
                    foreach($field as $key => $value){
                        # 判断数组传入结构是否与程序要求相同
                        if(is_array($value) and array_key_exists('format', $value)){
                            $as = null;
                            if(array_key_exists('as', $value)) $as = ' as '.$value['as'];
                            # 判断字段名是否符合命名规则
                            if(is_true($this->NameConfine, $key))
                                $this->Format .= ",format($key,{$value["format"]})$as";
                        }
                    }
                }else {
                    # 当传入值为字符串结构，判断字段名是否符合命名规则
                    if (is_true($this->NameConfine, $field) and is_true($regular, $format))
                        $this->Format = ",format($field,$format)";
                }
                break;
            default:
                break;
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Having 函数应用表达式
     */
    protected $Having = null;

    /**
     * 函数结构应用, 单项内容操作
     * @access public
     * @param string $func 函数
     * @param string $field 字段名
     * @param string $symbol 符号
     * @param int $value 参数值
     * @return object 返回数据库连接对象
    */
    function having(string $func, string $field, string $symbol, int $value): object
    {
        /**
         * 因为having运算主要用于范围所以当前版本仅支持对数字运算
        */
        # 创建可调用函数正则
        $regular_function_confine = '/^(avg|sum|max|min|len)$/';
        # 创建运算符匹配正则
        $regular_symbol_confine = '/^(gt|lt|eq|ge|le|neq)$/';
        # 判断参数是否符合预限定结果
        if(is_true($regular_function_confine, $func)){
            if(is_true($this->NameConfine, $field)){
                if(is_true($regular_symbol_confine, $symbol)){
                    $symbols = array('gt' => '>', 'lt' => '<', 'et'=> '=', 'eq' => '==','neq' => '!=', 'ge' => '>=',
                        'le' => '<=','heq' => '===', 'nheq' => '!==');
                    if(array_key_exists(trim(strtolower($symbol)), $symbols))
                        $symbol = $symbols[trim(strtolower($symbol))];
                    # 创建having信息数组
                    $this->Having = " having $func($field) $symbol $value";
                }
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Order 排序,与where功能支持相似
     */
    protected $Order = null;

    /**
     * 查询语句排序条件
     * @access public
     * @param string $field 字段名（列表list）
     * @param string $type 排序列表，默认 asc 升序，desc降序
     * @return object 返回数据库连接对象
    */
    function order(string $field, string $type='asc')
    {
        /**
         * 使用字符串作为唯一数据类型，通过对参数进行验证，判断参数数据结构
        */
        # 创建order结构正则变量
        $regular_order = '/^([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`|[^\_\W]+\(([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\))\s(asc|desc)((\,\s?[^\_\W]+(\_[^\_\W]+)*|\,\`.+[^\s]+\`|\,\s?[^\_\W]+\(([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\))\s(asc|desc))*$/';
        # 创建排序参数变量
        $regular_order_confine = '/^(asc|desc)$/';
        # 判断排序信息
        if(is_array($field)){
            $i = 0;
            foreach($field as $key => $type){
                if($i == 0)
                    $this->Order .= " order by $key $type";
                else
                    $this->Order .= ",$key $type";
                $i++;
            }

        }else{
            if(is_true($regular_order, $field))
                $this->Order = ' order by '.$field;
            else{
                if(is_true($this->NameConfine, $field)){
                    if(is_true($regular_order_confine, $type))
                        $this->Order = " order by $field $type";
                }
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $Limit 查询界限值，int或者带两组数字的字符串
     */
    protected $Limit = null;

    /**
     * 查询语句查询限制，有两个参数构成，起始位置，显示长度
     * @access public
     * @param int $start 起始位置
     * @param int $length 读取长度
     * @return object 返回数据库连接对象
    */
    function limit(int $start, int $length=0): object
    {
        if($start >= 0){
            if(is_int($length) and $length > 0){
                switch($this->DataType){
                    case self::RESOURCE_TYPE_PGSQL:
                    case self::RESOURCE_TYPE_SQLITE:
                        $this->Limit = " limit $length offset $start";
                        break;
                    case self::RESOURCE_TYPE_MSSQL: # mssql不支持limit语法
                        break;
                    case self::RESOURCE_TYPE_ORACLE:
                        $this->Limit = " rownum <= $start";
                        break;
                    case self::RESOURCE_TYPE_MARIADB:
                    default:
                        $this->Limit = " limit $start,$length";
                        break;
                }
            }else{
                switch($this->DataType){
                    case self::RESOURCE_TYPE_ORACLE:
                        if($start > 0) $this->Limit = " rownum <= $start";
                        break;
                    case self::RESOURCE_TYPE_MSSQL: # mssql不支持limit语法
                        break;
                    default:
                        if($start > 0) $this->Limit = " limit $start";
                }
            }
        }
        return $this->__getSQL();
    }

    /**
     * @access protected
     * @var string $FetchType 查询输出类型，包含3种基本参数，all：完整结构模式，nv：自然数结构模式，kv：字典结构模式
     */
    protected $FetchType = self::FETCH_NORMAL;

    /**
     * 加载列表显示结构限制
     * @access public
     * @param mixed $fetch_type 查询结果显示方式，默认值：FETCH_NORMAL<0>
     * 默认读取数据结构类型 <0>: FETCH_NORMAL
     * 整数与值数据结构类型 <1>: FETCH_NUMBER_VALUE
     * 键名与值数据结构类型 <2>: FETCH_KEY_VALUE
     * @return object 返回数据库连接对象
    */
    function fetch($fetch_type=self::FETCH_NORMAL): object
    {
        $types = array(self::FETCH_NORMAL,self::FETCH_NUMBER_VALUE,self::FETCH_KEY_VALUE);
        if(in_array(strtolower(trim($fetch_type)),$types))
            $this->FetchType = strtolower(trim($fetch_type));
        else {
            if (intval($fetch_type) < 3)
                $this->FetchType = $types[intval($fetch_type)];
        }
        return $this->__getSQL();
    }

    /**
     * 查询总数结构方法，返回一个整数结果
     * @access public
     * @return int 返回索引数据条数
    */
    abstract function count(): int;

    /**
     * 查询表格信息方法，并返回数组结果集
     * @access public
     * @return array 返回查询结果数组
     */
    abstract function select(): array;

    /**
     * 向表插入信息方法，并返回插入成功后插入后数据id
     * @access public
     * @return int 返回数据插入主键值
    */
    abstract function insert(): int;

    /**
     * 删除指定数据记录，并返回执行结果信息
     * @access public
     * @return int 返回影响数据条数
    */
    abstract function delete(): int;
    /**
     * 修改指定数据记录，并返回执行结果信息
     * @access public
     * @return int 返回影响数据条数
     */
    abstract function update(): int;

    /**
     * 执行自定义查询语句,并返回执行结果
     * @param string $query
     * @return array|int 返回语句执行结果
    */
    abstract function query(string $query);

    /**
     * 返回错误信息
     * @access public
     * @return string|null 返回异常信息
    */
    function getErrorMsg(): ?string
    {
        return $this->ErrMsg;
    }
}