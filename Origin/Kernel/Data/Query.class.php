<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Data.Query *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/01/09 11:04
 * update Time: 2017/01/09 14:59
 * chinese Context: IoC Sql操作封装类
 */
namespace Origin\Kernel\Data;
/**
 * 封装类，数据库操作，主结构访问类
 */
abstract class Query
{
    /**
     * SQL基础验证正则表达式变量
     * @var string $_Regular_Name_Confine
     * @var string $_Regular_Comma_Confine_Confine
     * @var string $_Regular_Period_Confine
     * @var string $_Regular_Exp_Confine_Confine
     * @var string $_Regular_Name
     * @var string $_Regular_Period
     */
    protected $_Regular_Name_Confine = '/^([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)$/';
    protected $_Regular_Comma_Confine = '/^([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)(\,\s?[^\_\W]+(\_[^\_\W]+)*|\,\`.+[^\s]+\`)*$/';
    protected $_Regular_Period_Confine = '/^(([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\.)([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)$/';
    protected $_Regular_Exp_Confine = '/^(([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\.)?([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)
                                     \s(eq)\s
                                     (([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\.)?([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)$/';
    protected $_Regular_Name = '/([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)/';
    protected $_Regular_Period = '/(([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)\.)?([^\_\W]+(\_[^\_\W]+)*|\`.+[^\s]+\`)/';
    /**
     * @var object $_Object
     * 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $_Object = null;
    /**
     * @var string $_Table 数据库表名，
     * 表名与映射结构及Model结构可以同时使用，当使用映射结构和model结构时，表名只做辅助
     */
    protected $_Table = null;
    protected $_As_Table = null;
    /**
     * @var string $_Query 查询语句
     * 当预设功能无法满足实际要求时，可以直接使用查询语句，
     * 该功能与映射结构及Model结构一致，但推荐是用映射及Model结构开发
     */
    protected $_Query = null;
    /**
     * @var string $_Top
     * 用于select查询中Top应用
     */
    protected $_Top = null;
    /**
     * @var string $_Total
     * 用于select查询中求总数，语句结构利用count函数
     */
    protected $_Total = null;
    /**
     * @var string $_Field 查询元素
     * 用于在select查询中精确查寻数据, 支持数组格式，同时支持as关键字
     */
    protected $_Field = '*';
    /**
     * @var string $_Distinct 查询单字段不重复值
     */
    protected $_Distinct = null;
    /**
     * @var string $_JoinOn 多表联合匹配条件
     */
    protected $_JoinOn = null;
    /**
     * @var array 连接表名
     */
    protected $_JoinTable = null;
    /**
     * @var array $_Union 低效相同列，相同数，支持单个或多个
     */
    protected $_Union = null;
    /**
     * @var array $_Data
     * 用户存储需要修改或者添加的数据信息，该模块与验证模块连接使用
     */
    protected $_Data = null;
    /**
     * @var mixed $_Where
     * sql语句条件变量，分别为两种数据类型，当为字符串时，直接引用，当为数组时，转化执行
     */
    protected $_Where = null;
    /**
     * @var mixed $_Group
     * 分组变量，与where功能支持相似
     */
    protected $_Group = null;
    /**
     * @var string $_Avg
     * 求平均数函数的字段名
     */
    protected $_Avg = null;
    /**
     * @var string $_First
     * 指定字段下第一个记录值的字段名
     */
    protected $_First = null;
    /**
     * @var string $_Last
     * 指定字段下最后一个记录值的字段名
     */
    protected $_Last = null;
    /**
     * @var string $_Max
     * 指定字段下最大记录值的字段名
     */
    protected $_Max = null;
    /**
     * @var string $_min
     * 指定字段下最小记录值的字段名
     */
    protected $_Min = null;
    /**
     * @var string $_Sum
     * 计算字段下所有列数值总和的字段名
     */
    protected $_Sum = null;
    /**
     * @var mixed $_Uppercase
     * 需返回信息中所有字母大写的字段名，返回值为数组
     */
    protected $_UpperCase = null;
    /**
     * @var mixed $_Lowercase
     * 需返回信息中所有字母小写的字段名，返回值为数组
     */
    protected $_LowerCase = null;
    /**
     * @var array $_Mid
     * 返回指定字段截取字符特定长度的信息，数组类型
    */
    protected $_Mid = null;
    /**
     * @var mixed $_Len
     * 计算指定字段记录值长度的字段名,同时支持字符串和数组类型
    */
    protected $_Len = null;
    /**
     * @var mixed $_Length
     * 计算指定字段记录值长度的字段名,同时支持字符串和数组类型
     */
    protected $_Length = null;
    /**
     * @var array $_Round
     * 需进行指定小数点长度的四舍五入计算的字段名及截取长度数组
    */
    protected $_Round = null;
    /**
     * @var string $_Now
     * 获取数据库当前时间
    */
    protected $_Now = null;
    /**
     * @var array $_Format
     * 需进行格式化的记录的字段名及格式信息数组
    */
    protected $_Format = null;
    /**
     * @var string $_Having
     * 函数应用表达式
     */
    protected $_Having = null;
    /**
     * @var string $_Order
     * 排序,与where功能支持相似
     */
    protected $_Order = null;
    /**
     * @var mixed $_Limit
     * 查询界限值，int或者带两组数字的字符串
     */
    protected $_Limit = null;
    /**
     * 构造器函数
    */
    function __construct()
    {}
    /**
     * 表名获取方法
     * @access public
     * @param mixed $table
     * @return object
     */
    function table($table,$major)
    {
        /**
         * 根据SQL命名规范，及同行开发要求对表名信息进行，基本过滤验证
         * 支持as语句，使用数组及字符串双重结构管理，当需要进行别名设置时，使用数组结构
         * @var string $_key
         * @var string $_value
         */
        # 判断传入执行类型
        if(is_array($table)){
            # 判断数组元素总数
            if(count($table)){
                # 创建计数变量
                $i = 0;
                # 使用foreach函数进行数组遍历
                foreach($table as $_key => $_value){
                    # 判断字段名和简名是否和规
                    if(is_true($this->_Regular_Comma_Confine, $_key)  === true and is_true($this->_Regular_Comma_Confine, $_value)  === true){
                        if($i == 0){
                            $this->_Table = ' '.$_key.' as '.$_value;
                            if(count($table) > 1){
                                $this->_As_Table = array();
                                array_push($this->_As_Table,$_value);
                            }else{
                                $this->_As_Table = $_value;
                            }
                        }else{
                            $this->_Table = ', '.$_key.' as '.$_value;
                            array_push($this->_As_Table,$_value);
                        }
                    }else{
                        # 当只有字段名命名合规时，只装载字段名，不装载简名
                        if(is_true($this->_Regular_Comma_Confine, $_key) === true){
                            if($i == 0){
                                $this->_Table = ' '.$_key;
                                if(count($table) > 1){
                                    $this->_As_Table = array();
                                    array_push($this->_As_Table,$_key);
                                }else{
                                    $this->_As_Table = $_key;
                                }
                            }else{
                                $this->_Table = ', '.$_key;
                                array_push($this->_As_Table,$_key);
                            }
                        }else{
                            # 异常处理：表格名称不符合命名规范
                            try{
                                throw new \Exception('Table name is not in conformity with the naming conventions');
                            }catch(\Exception $e){
                                var_dump(debug_backtrace(0,1));
                                echo("<br />");
                                echo('Origin (Query) Class Error: '.$e->getMessage());
                                exit(0);
                            }
                        }
                    }
                }
            }
        }else{
            # 根据SQL数据库命名规则判断数据表名是否符合规则要求，如果符合装在进SQL模块Table变量中
            if(is_true($this->_Regular_Comma_Confine, $table) === true){
                $this->_Table = strtolower($table);
            }else{
                # 异常处理：表格名称不符合命名规范
                try{
                    throw new \Exception('Table name is not in conformity with the naming conventions');
                }catch(\Exception $e){
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (Query) Class Error: '.$e->getMessage());
                    exit(0);
                }
            }
        }
        return $this->__getSQL();
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
     * 查询语句获取方法，实际开发中由于query模块不对外
     * 语句在执行时进行过滤保护，数据语句结构上不进行强过滤保护
     * 下一个版本中加添加语句结构限制等功能
     * @access public
     * @param string $query
     * @return object
     */
    function query($query)
    {
        $this->_Query = $query;
        return $this->__getSQL();
    }
    /**
     * Top 语句结构
     * @access public
     * @param $number
     * @param $percent
     * @return object
     */
    function top($number, $percent=false)
    {
        # top 关键字后边只能接数组，所以在拼接语句时，要对number进行类型转化
        $this->_Top .=' top '.intval($number);
        # 判断是否使用百分比进行查询
        if($percent and intval($number) > 100)
            $this->_Top = ' top 100 percent';
        elseif($percent and intval($number) <= 100)
            $this->_Top .= ' percent';
        return $this->__getSQL();
    }
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
                    $this->_Total .= $_symbol.'count('.$field[$_i].')';
                }else{
                    $this->_Total .= $_symbol.'count('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Total = ' count('.$field.')';
        }
        return $this->__getSQL();
    }
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
                    if(is_true($this->_Regular_Comma_Confine, $_value) === true) {
                        if(is_true($this->_Regular_Comma_Confine, $_key) === true and !is_numeric($_key)){
                            # 判断计数变量当前值，等于0时不添加连接符号
                            if ($i == 0)
                                $this->_Field = ' '.$_key.' as '.$_value;
                            else
                                $this->_Field .= ','.$_key.' as '.$_value;
                        }else{
                            if($i == 0)
                                $this->_Field = $_value;
                            else
                                $this->_Field .= ', '.$_value;
                        }
                        $i += 1;
                    }else{
                        # 异常处理：字段名不符合命名规范
                        try{
                            throw new \Exception('Field name is not in conformity with the naming conventions');
                        }catch(\Exception $e){
                            var_dump(debug_backtrace(0,1));
                            echo("<br />");
                            echo('Origin (Query) Class Error: '.$e->getMessage());
                            exit(0);
                        }
                    }
                }
            }
        }else{
            # 根据SQL数据库命名规则判断字段名是否符合规则要求，如果符合装在进SQL模块Field变量中
            if(is_true($this->_Regular_Comma_Confine, $field) === true)
                $this->_Field = $field;
            else{
                # 异常处理：字段名称不符合命名规范
                try{
                    throw new \Exception('Field name is not in conformity with the naming conventions');
                }catch(\Exception $e){
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (Query) Class Error: '.$e->getMessage());
                    exit(0);
                }
            }
        }
        return $this->__getSQL();
    }
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
        if(is_true($this->_Regular_Name_Confine, $field) === true)
            $this->_Distinct = ' distinct '.$field;
        return $this->__getSQL();
    }
    /**
     * 多表关系匹配 join 语句，支持多表联查，根据join特性join后接表名为单表
     * 多表联合匹配条件 on，与join联合使用，当field只有一个值时，系统会自动调用表格中，同名字段名
     * 当有多个条件时，可以使用数组进行结构导入
     * @access public
     * @param string|array $table
     * @param string|array $field
     * @param string $type Join关系方式分别为 inner，left，right，full
     * @return object
     */
//    function join($table,$field, $type='inner')
//    {
//        /**
//         * 进行传入值结构判断
//         */
//        # 限定join类型，目的是防止误操作
//        $_regular_type = '/^(inner|left|right|full|cross|straight)$/';
//        if(is_array($table)){
//            if(is_numeric(array_keys($table)[0])){
//                for($_i = 0;$_i < count($table);$_i++){
//                }
//            }else{
//                foreach($table as $_key => $_value){
//                }
//            }
//        }else{
//            if(is_true($this->_Regular_Name,$table)){
//            }
//        }
//        return $this->__getSQL();
//    }
    /**
     * 对多个查询语句的相同字段中的相同数据进行合并,当前版本仅支持两个表单字段查询，并对输入结构进行验证和隔离
     * @access public
     * @param string $table
     * @param string $field
     * @return object
     */
    function union($table, $field)
    {
        /**
         * 使用SQL命名规则对输入的表名和字段名进行验证
         */
        # 判断传入参数表名和字段名是否符合命名规则
        if(is_true($this->_Regular_Name_Confine, $table) === true and is_true($this->_Regular_Name_Confine, $field) === true)
            $this->_Union = ' union select '.$field.' from '.$table;
        else{
            if(is_true($this->_Regular_Name_Confine, $table) === true){
                # 异常处理：字段名不符合SQL命名规则
                try{
                    throw new \Exception('The field name is not in conformity with the SQL naming rules');
                }catch(\Exception $e){
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (Query) Class Error: '.$e->getMessage());
                    exit(0);
                }
            }else{
                # 异常处理：表名名不符合SQL命名规则
                try{
                    throw new \Exception('The table name is not in conformity with the SQL naming rules');
                }catch(\Exception $e){
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (Query) Class Error: '.$e->getMessage());
                    exit(0);
                }
            }
        }
        return $this->__getSQL();
    }
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
                if(is_true($this->_Regular_Name_Confine, $_key) === true){
                    if(!is_array($this->_Data)) $this->_Data = array();
                    array_push($this->_Data, array($_key => $_value));
                    # this->_Data[$_key] = $_value;
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new \Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(\Exception $e){
                        var_dump(debug_backtrace(0,1));
                        echo("<br />");
                        echo('Origin (Query) Class Error: '.$e->getMessage());
                        exit(0);
                    }
                }
            }
        }else{
            # 异常处理：参数结构需使用数组
            try{
                throw new \Exception('Need to use an array parameter structure');
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                echo('Origin (Query) Class Error: '.$e->getMessage());
                exit(0);
            }
        }
        return $this->__getSQL();
    }
    /**
     * 条件信息加载方法，传入值类型支持字符串、数组
     * 当为数组key为字段名，数组value为条件值，数组类型下，仅支持等于条件
     * 当为字符串，要求条件信息符合SQL语句规则
     * @access public
     * @param mixed $field
     * @return object
     */
    function where($field)
    {
        /**
         * 区别数据类型使用SQL命名规则对输入的字段名进行验证
         */
        if(is_array($field)){# 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            $_i = 0;
            foreach($field as $_key => $_value){
                if(is_true($this->_Regular_Name_Confine, $_key) === true)
                    # 将数组信息存入类变量
                    if($_i == 0){
                        if(is_integer($_value) or is_float($_value) or is_double($_value)){
                            $this->_Where = ' where '.$_key.'='.$_value;
                        }else{
                            $this->_Where = ' where '.$_key.'=\''.$_value.'\'';
                        }

                    }else{
                        if(is_int($_value) or is_float($_value) or is_double($_value)){
                            $this->_Where .= ' and '.$_key.'='.$_value;
                        }else{
                            $this->_Where .= ' and '.$_key.'=\''.$_value.'\'';
                        }
                    }
                else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new \Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(\Exception $e){
                        var_dump(debug_backtrace(0,1));
                        echo("<br />");
                        echo('Origin (Query) Class Error: '.$e->getMessage());
                        exit(0);
                    }
                }
                $_i++;
            }
        }else{
            # 对输入字符串进行特殊字符转义，降低XSS攻击
            # 用预设逻辑语法数组替代特殊运算符号
            foreach(array(' gt ' => '>', ' lt ' => '<',' neq ' => '!=', ' eq '=> '=', ' ge ' => '>=', ' le ' => '<=') as $key => $value){
                $field = str_replace($key, $value, $field);
            }
            $this->_Where = ' where '.$field;
        }
        return $this->__getSQL();
    }
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
         * @var string int $_key
         * @var mixed $_value
         */
        # 判断传入参数类型
        if(is_array($field)){
            # 创建编辑变量
            $_i = 0;
            # 循环遍历数组内元素信息
            foreach($field as  $_key => $_value){
                # 验证元素信息值是否符合SQL命名规则
                if(is_true($this->_Regular_Name_Confine, $_value) === true){
                    # 拼接条件信息
                    if($_i == 0)
                        $this->_Group = ' group by '.$_value;
                    else
                        $this->_Group .= ', '.$_value;

                    $_i++;
                }else{
                    # 异常处理：字段名不符合SQL命名规则
                    try{
                        throw new \Exception('The field name is not in conformity with the SQL naming rules');
                    }catch(\Exception $e){
                        var_dump(debug_backtrace(0,1));
                        echo("<br />");
                        echo('Origin (Query) Class Error: '.$e->getMessage());
                        exit(0);
                    }
                }
            }
        }else{
            # 使用多条件结构正则验证字符串内容
            if(is_true($this->_Regular_Comma_Confine, $field) === true)
                $this->_Group = $field;
            else{
                # 异常处理：GROUP语法字段名结构不符合SQL使用规则
                try{
                    throw new \Exception('Group of grammatical structure of the field name is not in
                                               conformity with the SQL using rules');
                }catch(\Exception $e){
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    echo('Origin (Query) Class Error: '.$e->getMessage());
                    exit(0);
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * 查询语句指定字段值平均数，支持单字段名
     * @access public
     * @param string $field
     * @return object
     */
    function avg($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Avg .= $_symbol.'avg('.$field[$_i].')';
                }else{
                    $this->_Avg .= $_symbol.'avg('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Avg = ', avg('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 查询语句指定字段值第一个记录值，支持单字段名
     * @access public
     * @param string $field
     * @return object
     */
    function first($field)
    {
        if(is_array($field)){
            $this->_First = ', first('.array_keys($field)[0].') as '.$field[array_keys($field)[0]];
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_First = ', first('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 查询语句指定字段值最后一个记录值，支持单字段名
     * @access public
     * @param string $field
     * @return object
     */
    function last($field)
    {
        if(is_array($field)){
            $this->_Last = ', last('.array_keys($field)[0].') as '.$field[array_keys($field)[0]];
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Last = ', last('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 查询语句指定字段中最大值，支持单字段名
     * @access public
     * @param string $field
     * @return object
     */
    function max($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Max .= $_symbol.'max('.$field[$_i].')';
                }else{
                    $this->_Max .= $_symbol.'max('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Max = ' max('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 查询语句指定字段中最小值，支持单字段名
     * @access public
     * @param string $field
     * @return object
     */
    function min($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Min .= $_symbol.'min('.$field[$_i].')';
                }else{
                    $this->_Min .= $_symbol.'min('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Min = ' min('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 返回指定字段下所有列值的总和，只支持数字型字段列
     * @access public
     * @param string $field
     * @return object
     */
    function sum($field)
    {
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Sum .= $_symbol.'sum('.$field[$_i].')';
                }else{
                    $this->_Sum .= $_symbol.'sum('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Sum = ' sum('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 返回指定字段信息中的字母全部大写，支持数组及字符串
     * 当含有多个字段名，使用数组，单个字段使用字符串
     * @access public
     * @param mixed $field
     * @return object
     */
    function toUpper($field)
    {
        /**
         * 区别数据类型使用SQL命名规则对输入的字段名进行验证
         */
        if(is_array($field)){
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_LowerCase .= $_symbol.'ucase('.$field[$_i].')';
                }else{
                    $this->_LowerCase .= $_symbol.'ucase('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_UpperCase = ', ucase('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 返回指定字段信息中的字母全部小写，支持数组及字符串
     * 当含有多个字段名，使用数组，单个字段使用字符串
     * @access public
     * @param mixed $field
     * @return object
     */
    function toLower($field)
    {
        /**
         * 区别数据类型使用SQL命名规则对输入的字段名进行验证
         */
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_LowerCase .= $_symbol.'lcase('.$field[$_i].')';
                }else{
                    $this->_LowerCase .= $_symbol.'lcase('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_LowerCase = ', lcase('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 查询语句对指定字段进行截取
     * @access public
     * @param $field
     * @param $start
     * @param $length
     * @return object
    */
    function mid($field, $start=0, $length=0)
    {
        /**
         * @var string $_key
         * @var array $_value
        */
        # 判断数据类型
        if(is_array($field)){
            # 变量数组信息
            foreach($field as $_key => $_value){
                # 判断数组传入结构是否与程序要求相同
                if(is_array($_value) and array_key_exists('start', $_value) and array_key_exists('length', $_value)){
                    $_as = null;
                    if(array_key_exists('as', $_value)) $_as = ' as '.$_value['as'];
                    # 判断字段名是否符合命名规则
                    if(is_true($this->_Regular_Name_Confine, $_key) === true){
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
            if (is_true($this->_Regular_Name_Confine, $field) === true and $start >= 0){
                if ($length > 0){
                    $this->_Mid = ', mid(' . $field . ','.intval($start).','.intval($length).')';
                }else {
                    $this->_Mid = ', mid(' . $field . ',' . intval($start) . ')';
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * 计算指定字段列记录值长度，一般只应用于文本格式信息
     * 方法支持两种数据类型，如果只对一个字段进行操作，使用字符串类型
     * 对多个字段进行操作，则使用自然数标记数组
     * @access public
     * @param string $field
     * @return object
    */
    function len($field)
    {
        /**
         * @var array $_value
         */
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Len .= $_symbol.'len('.$field[$_i].')';
                }else{
                    $this->_Len .= $_symbol.'len('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Len  = ', len('.$field.')';
        }
        return $this->__getSQL();
    }
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
        /**
         * @var array $_value
         */
        if(is_array($field)){
            # 遍历数组，并对数组key值进行验证，如果不符合命名规则，抛出异常信息
            for($_i=0;$_i<count($field);$_i++){
                $_symbol = '';
                if($_i!=0) $_symbol = ',';
                if(is_numeric(array_keys($field)[0])){
                    $this->_Len .= $_symbol.'length('.$field[$_i].')';
                }else{
                    $this->_Len .= $_symbol.'length('.array_keys($field)[$_i].') as '.$field[array_keys($field)[$_i]];
                }
            }
        }else{
            if(is_true($this->_Regular_Name_Confine, $field) === true)
                $this->_Len  = ', length('.$field.')';
        }
        return $this->__getSQL();
    }
    /**
     * 对指定字段进行限定小数长度的四舍五入运算
     * 参数同时支持
     * @access public
     * @param mixed $field
     * @param int $decimals
     * @return object
    */
    function round($field, $decimals = 0)
    {
        /**
         * @var string $_key
         * @var array $_value
        */
        # 判断数据类型
        if(is_array($field)){
            # 变量数组信息
            foreach($field as $_key => $_value){
                # 判断数组传入结构是否与程序要求相同
                if(is_array($_value) and array_key_exists('decimals', $_value)){
                    $_as = null;
                    if(array_key_exists('as', $_value)) $_as = ' as '.$_value['as'];
                    # 判断字段名是否符合命名规则
                    if(is_true($this->_Regular_Name_Confine, $_key) === true){
                        if($_value['length'] > 0){
                            $this->_Round .= ', round(' . $_key . ','.intval($_value['start']).','.intval($_value['length']).')'.$_as;
                        }else{
                            $this->_Round .= ', round(' . $_key . ','.intval($_value['start']).')'.$_as;
                        }
                    }
                }
            }
        }else {
            # 当传入值为字符串结构，判断字段名是否符合命名规则
            if (is_true($this->_Regular_Name_Confine, $field) === true and $decimals >= 0){
                $this->_Round = ', round(' . $field . ','.intval($decimals).')';
            }
        }
        return $this->__getSQL();
    }
    /**
     * 返回当前数据库时间
     */
    function now()
    {
        $this->_Now = ', nowTime';
        return $this->__getSQL();
    }
    /**
     * 对指定字段记录进行格式化处理
     * @access public
     * @param mixed $field
     * @param string $format
     * @return object
    */
    function format($field, $format = null)
    {
        /**
         * 格式结构验证，仅对一般xss攻击进行符号过滤
         * @var string $_regular
         * @var string $_key
         * @var string $_value
        */
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
                    if(is_true($this->_Regular_Name_Confine, $_key) === true){
                        $this->_Format .= ', format(' . $_key . ','.$_value['format'].')'.$_as;
                    }
                }
            }
        }else {
            # 当传入值为字符串结构，判断字段名是否符合命名规则
            if (is_true($this->_Regular_Name_Confine, $field) === true and is_true($_regular, $format) === true)
                $this->_Format = ', format('.$field.','.$format.')';
        }
        return $this->__getSQL();
    }
    /**
     * 函数结构应用, 单项内容操作
     * @access public
     * @param string $function
     * @param string $field
     * @param string $symbol
     * @param int $value
     * @return object
    */
    function having($function='sum', $field, $symbol, $value)
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
        if(is_true($_regular_function_confine, $function) === true){
            if(is_true($this->_Regular_Name_Confine, $field) === true){
                if(is_true($_regular_symbol_confine, $symbol) === true){
                    if(is_numeric($value)){
                        # 创建having信息数组
                        $this->_Having = ' having '.$function.'('.$field.') '.Symbol($symbol).' '.$value;
                    }
                }
            }
        }
        return $this->__getSQL();
    }
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
                    $this->_Order .= ' order by '.$_key.' '.$_type;
                else
                    $this->_Order .= ', '.$_key.' '.$_type;
                $_i++;
            }

        }else{
            if(is_true($_regular_order, $field) === true)
                $this->_Order = ' order by '.$field;
            else{
                if(is_true($this->_Regular_Name_Confine, $field) === true){
                    if(is_true($_regular_order_confine, $type) === true){
                        $this->_Order = ' order by '.$field.' '.$type;
                    }
                }
            }
        }
        return $this->__getSQL();
    }
    /**
     * 查询语句查询限制，有两个参数构成，起始位置，显示长度
     * @access public
     * @param int $start
     * @param int $length
     * @return object
    */
    function limit($start, $length=0)
    {
        if(is_int($start) and $start > 0){
            if(is_int($length) and $length > 0){
                $this->_Limit = ' limit ('.$start.','.$length.')';
            }else{
                $this->_Limit = ' limit '.$start;
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
}