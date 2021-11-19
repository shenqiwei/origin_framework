<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架SQL执行语句封装类
 */
namespace Origin\Package;

use PDOException;
use PDO;

class Database extends Query
{
    /**
     * 操作常量
     * @access public
     */
    const QUERY_SELECT = "select";
    const QUERY_INSERT = "insert";
    const QUERY_UPDATE = "update";
    /**
     * @access private
     * @var PDO|object $Connect 数据库连接
     */
    private $Connect;

    /**
     * @access private
     * @var string $Select select 为起始词
     */
//    private $Select = '/^(select)\s(([^\s]+\s)+|\*)\s(from)\s.*/';

    /**
     * @access private
     * @var string $SelectCount 带count关键字段
     */
    private $SelectCount = '/^(select)\s(count\(([^\s]+\s)+|\*)\)\s(from)\s.*/';

    /**
     * @access private
     * @var string $From from 为起始词
     */
    private $From = '/^(from)\s.*/';

    /**
     * @access private
     * @var int $RowCount 获取select查询响应条数信息
    */
    private $RowCount = 0;

    /**
     * 构造函数，用于预加载数据源配置信息
     * @access public
     * @param string|null $connect_name 数据源配置名称
     * @param int $type 数据库类型，默认值 0 <mysql|mariadb>
     * @return void
    */
    function __construct(string $connect_name=null, int $type=0)
    {
        # 保存数据源类型
        $this->DataType = $type;
        # 获取配置信息
        $connect_config = config('DATA_MATRIX_CONFIG');
        if(is_array($connect_config)){
            for($i = 0;$i < count($connect_config);$i++){
                if(key_exists("DATA_NAME",$connect_config[$i]) and $connect_config[$i]['DATA_NAME'] === $connect_name){
                    $connect_conf = $connect_config[$i];
                    break;
                }
            }
            # 判断配置加载情况，如果失效则自动调用第一个配置信息数组
            if(!isset($connect_conf))
                $connect_config = $connect_config[0];
            else
                $connect_config = $connect_conf;
            switch($this->DataType){
                case self::RESOURCE_TYPE_PGSQL:
                    $DSN = "pgsql:host={$connect_config["DATA_HOST"]};port={$connect_config["DATA_PORT"]};dbname={$connect_config["DATA_DB"]}";
                    break;
                case self::RESOURCE_TYPE_MSSQL:
                    $DSN = "dblib:host={$connect_config["DATA_HOST"]}:{$connect_config["DATA_PORT"]};dbname={$connect_config["DATA_DB"]}";
                    break;
                case self::RESOURCE_TYPE_SQLITE:
                    $DSN = "sqlite:{$connect_config["DATA_DB"]}";
                    break;
                case self::RESOURCE_TYPE_ORACLE:
                    $oci = "(DESCRIPTION =
                            (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = {$connect_config["DATA_HOST"]})(PORT = {$connect_config["DATA_PORT"]})))
                            (CONNECT_DATA = (SERVICE_NAME = {$connect_config["DATA_DB"]}))";
                    $DSN = "oci:dbname=$oci";
                    break;
                case self::RESOURCE_TYPE_MYSQL:
                case self::RESOURCE_TYPE_MARIADB:
                default:
                    $DSN = "mysql:host={$connect_config["DATA_HOST"]};port={$connect_config["DATA_PORT"]};dbname={$connect_config["DATA_DB"]}";
                    break;
            }
            if($this->DataType != self::RESOURCE_TYPE_SQLITE){
                # 创建数据库链接地址，端口，应用数据库信息变量
                $username = $connect_config['DATA_USER']; # 数据库登录用户
                $password = $connect_config['DATA_PWD']; # 登录密码
                $option = array(
                    # 设置数据库编码规则
                    PDO::ATTR_PERSISTENT => true,
                );
                # 创建数据库连接对象
                $this->Connect = new PDO($DSN, $username, $password, $option);
            }else
                $this->Connect = new PDO($DSN);
            # 设置数据库参数信息
            $this->Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # 是否使用持久链接
            $this->Connect->setAttribute(PDO::ATTR_PERSISTENT,boolval($connect_config['DATA_P_CONNECT']));
            # SQL自动提交单语句
            if(in_array($this->DataType,array(self::RESOURCE_TYPE_ORACLE,self::RESOURCE_TYPE_MYSQL,self::RESOURCE_TYPE_MARIADB)))
                $this->Connect->setAttribute(PDO::ATTR_AUTOCOMMIT,boolval($connect_config['DATA_AUTO']));
            # SQL请求超时时间
            if(intval(config('DATA_TIMEOUT')))
                $this->Connect->setAttribute(PDO::ATTR_TIMEOUT,intval($connect_config['DATA_TIMEOUT']));
            # SQL是否使用缓冲查询
            if(config('DATA_USE_BUFFER')){
                if(in_array($this->DataType,array(self::RESOURCE_TYPE_MYSQL,self::RESOURCE_TYPE_MARIADB)))
                    $this->Connect->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,boolval($connect_config['DATA_USE_BUFFER']));
            }
        }
    }

    /**
     * 返回查询信息的总数
     * @access public
     * @return int 返回索引数据条数
     */
    function count(): int
    {
        $field = (!is_null($this->Field))?"count($this->Field)":"count(*)";
        # 起始结构
        $sql = "select $field from $this->Table $this->JoinOn $this->Union $this->Where";
        # 返回数据
        return intval($this->query($sql)[0][0]);
    }

    /**
     * 查询信息函数
     * @access public
     * @return array 返回索引结果数组
     */
    function select()
    {
        # 求总和
        if(!is_null($this->Total)){
            if(!is_null($this->Field))
                $this->Total = ','.$this->Total;
        }
        # 平均数信息 与field冲突，需要group by配合使用
        if(!is_null($this->Avg)){
            if(!is_null($this->Total))
                $this->Avg = ','.$this->Avg;
        }
        # 最大值 与field冲突，需要group by配合使用
        if(!is_null($this->Max)){
            if(!is_null($this->Total) or !is_null($this->Avg))
                $this->Max = ','.$this->Max;

        }
        # 最小值 与field冲突，需要group by配合使用
        if(!is_null($this->Min)){
            if(!is_null($this->Total) or !is_null($this->Avg) or !is_null($this->Max))
                $this->Min = ','.$this->Min;
        }
        # 求和 与field冲突，需要group by配合使用
        if(!is_null($this->Sum)){
            if(!is_null($this->Total) or !is_null($this->Avg) or !is_null($this->Max) or !is_null($this->Min))
                $this->Sum = ','.$this->Sum;
        }
        # 添加查询头
        # 添加查询头
        $sql = "select $this->Field$this->Top$this->Total$this->Avg$this->Max$this->Min$this->Sum$this->Abs$this->Mod".
                "$this->Random$this->LTrim$this->Trim$this->RTrim$this->Replace$this->UpperCase$this->LowerCase".
                "$this->Mid$this->Length$this->Round$this->Now$this->Format$this->Distinct".
                " from $this->Table $this->JoinOn $this->AsTable $this->Union $this->Where $this->Group".
                " $this->Order $this->Having $this->Limit";
        # 返回数据
        return $this->query($sql);
    }

    /**
     * 插入信息函数
     * @access public
     * @return int 返回插入数据的主键值
     */
    function insert()
    {
        $columns = null;
        $values = null;
        for($i = 0; $i < count($this->Data); $i++){
            foreach($this->Data[$i] as $key => $value){
                if($i == 0){
                    $columns = $key;
                    if(is_integer($value) or is_float($value) or is_double($value))
                        $values = $value;
                    else
                        $values = '\''.$value.'\'';
                }else{
                    $columns .= ','.$key;
                    if(is_integer($value) or is_float($value) or is_double($value))
                        $values .= ','.$value;
                    else
                        $values .= ',\''.$value.'\'';
                }
            }
        }
        # 执行主函数
        $sql = "insert into $this->Table ($columns)value($values)";
        # 返回数据
        return $this->query($sql);
    }

    /**
     * 修改信息函数
     * @access public
     * @return int 返回影响数据数量
     */
    function update()
    {
        $columns = null;
        for($i = 0; $i < count($this->Data); $i++){
            foreach($this->Data[$i] as $key => $value){
                if($i == 0){
                    if(is_integer($value) or is_float($value) or is_double($value))
                        $columns = $key.'='.$value;
                    else
                        $columns = $key.'=\''.$value.'\'';
                }else{
                    if(is_integer($value) or is_float($value) or is_double($value))
                        $columns .= ','.$key.'='.$value;
                    else
                        $columns .= ','.$key.'=\''.$value.'\'';
                }
            }
        }
        # 执行主函数
        $sql = "update $this->Table set $columns $this->Where";
        # 返回数据
        return $this->query($sql);
    }

    /**
     * 删除信息函数
     * @access public
     * @return int 返回影响数据数量
     */
    function delete()
    {
        # 执行主函数
        $sql = "delete from $this->Table $this->Where";
        # 返回数据
        return $this->query($sql);
    }

    /**
     * 自定义语句执行函数
     * @access public
     * @param string $query sql语句
     * @return array|int 返回语句执行内容
     */
    function query(string $query)
    {
        # 创建返回信息变量
        if(is_true($this->SelectCount, strtolower($query)))
            $select_count = null;
        elseif(is_true($this->From, strtolower($query)))
            $query = 'select * '.strtolower($query);
        if(strpos(strtolower($query),"select ") === 0)
            $query_type = self::QUERY_SELECT;
        elseif(strpos(strtolower($query),"insert ") === 0)
            $query_type = self::QUERY_INSERT;
        else
            $query_type = self::QUERY_UPDATE;
        # 事务状态
        if(config("DATA_USE_TRANSACTION") and $query_type != 'select')
            $this->Connect->beginTransaction();
        # 条件运算结构转义
        foreach(array('/\s+gt\s+/' => '>', '/\s+lt\s+/ ' => '<','/\s+neq\s+/' => '!=', '/\s+eq\s+/'=> '=',
                    '/\s+ge\s+/' => '>=', '/\s+le\s+/' => '<=') as $key => $value)
            $query = preg_replace($key, $value, $query);
        # 接入执行日志
        $uri = LOG_CONNECT.date('Ymd').'.log';
        $model_msg = date("Y/m/d H:i:s")." [Note]: ".trim($query).PHP_EOL;
        _log($uri,$model_msg);
        try{
            # 执行查询搜索
            $statement = $this->Connect->query(trim($query));
            # 返回查询结构
            if($query_type === self::QUERY_SELECT){
                # 回写select查询条数
                $this->RowCount = $statement->rowCount();
                if($this->FetchType === self::FETCH_NUMBER_VALUE)
                    $receipt = $statement->fetchAll(PDO::FETCH_NUM);
                elseif($this->FetchType === self::FETCH_KEY_VALUE)
                    $receipt = $statement->fetchAll(PDO::FETCH_ASSOC);
                else{
                    if(isset($select_count))
                        $receipt = $statement->fetchAll(PDO::FETCH_COLUMN)[0];
                    else
                        $receipt = $statement->fetchAll();
                }
            }elseif($query_type === self::QUERY_INSERT)
                $receipt = $this->Connect->lastInsertId($this->Primary);
            else
                $receipt = $statement->rowCount();
            # 释放连接
            $statement->closeCursor();
        }catch(PDOException $e){
            errorLog($e->getMessage());
            exception("SQL Error",$this->Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        return $receipt;
    }

    /**
     * 执行事务提交
     * @access public
     * @return void
     */
    function getCommit()
    {
        $this->Connect->commit();
    }

    /**
     * 执行事务回滚
     * @access public
     * @return void
     */
    function getRollBack()
    {
        $this->Connect->rollBack();
    }

    /**
     * 返回select查询条数信息
     * @access public
     * @return int 返回语句数量
     */
    function getRowCount(): int
    {
        return $this->RowCount;
    }

    /**
     * 分页函数
     * @access public
     * @param string $url 链接
     * @param int $count 总数
     * @param int $current 当前页
     * @param int $row 分页大小
     * @param string|null $search 搜索条件
     * @return array 返回分页结构数组
     */
    function paging(string $url, int $count, int $current=1, int $row=10, ?string $search=null): array
    {
        $page=array(
            # 基本参数
            'url'=>$url, # 连接地址
            'count'=>0, # 总数
            'current'=>1, # 当前页码
            'begin'=>0, # 当前列表起始位置
            'limit'=>$row, # 显示数量
            'page_begin' => ($current-1)*$row+1,
            'page_count' => $current*$row,
            'search' => $search, # 搜索内容
            # 连接参数
            'first_url'=>'','first'=>0, # 第一页参数
            'last_url'=>'','last'=>0, # 上一页参数
            'next_url'=>'','next'=>0, # 下一页参数
            'end_url'=>'','end'=>0, # 最后一页参数
            # number结构参数
            'num_begin'=>0, # Number区间显示翻页页码起始位置
            'num_end'=>0, # Number区间显示翻页页码结束位置
        );
        $page['current']=$current;
        $page['count']=$count%$page['limit']!=0?intval(($count/$page['limit'])+1):intval($count/$page['limit']);
        # 判断页标状态
        if($page['current']<=0) $page['current']=1;
        if($page['current']>$page['count']) $page['current']=$page['count'];
        if($page['count']<=0) $page['current']=$page['count']=1;
        $page['begin']=$page['limit']*($page['current']-1);//其实点运算
        $page['page_one']=$page['limit']+1;
        $page['page_end']=($page['limit']+$page['size'])>$count?$count:$page['limit']+$page['size'];
        # 判断翻页状态1
        if($page['current']>1)
            $page['last']=$page['current']-1;
        else
            $page['last']=1;
        # 判断翻页状态2
        if($page['current']>=$page['count'])
            $page['next']=$page['count'];
        else
            $page['next']=$page['current']+1;
        $page['first_url']=$page['url'].'?page=1'.$page["search"];//第一页
        $page['last_url']=$page['url'].'?page='.$page['last'].$page["search"];//上一页
        $page['next_url']=$page['url'].'?page='.$page['next'].$page["search"];//下一页
        $page['end_url']=$page['url'].'?page='.$page['count'].$page["search"];//最后一页
        return $page;
    }

    /**
     * 页脚扩展函数
     * @access public
     * @param array $page 分页数组
     * @param int $cols 页码数量
     * @return array 返回分页页脚结构数据
     */
    function footer(array $page, int $cols=5): array
    {
        //执行数字页码
        $n=array();
        if($page['count']>$cols){
            $k=($cols%2==0)?$cols/2:($cols-1)/2;
            if(($page['current']-$k)>1 && ($page['current']+$k)<$page['count']){
                $page['num_begin']=$page['current']-$k;
                $page['num_end']=$page['current']+$k;
            }else{
                if(($page['current']+$k)>=$page['count']){
                    $page['num_begin']=$page['count']-($cols-1);
                    $page['num_end']=$page['count'];
                }else{
                    $page['num_begin']=1;
                    $page['num_end']=$cols;
                }
            }
            for($i=$page['num_begin'];$i<=$page['num_end'];$i++)
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
        }else{
            for($i=1;$i<=$page['count'];$i++)
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
        }
        return $n;
    }

    /**
     * 析构函数：数据库链接释放
     * @access public
     * @return void
     */
    function __destruct()
    {
        $this->Connect = null;
    }
}