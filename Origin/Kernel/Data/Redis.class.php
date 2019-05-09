<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Data.Redis *
 * version: 1.0 *
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2018
 * @context: IoC Redis封装类
 */
namespace Origin\Kernel\Data;

include("Redis/Key.class.php");
include("Redis/Str.class.php");
include("Redis/Set.class.php");
include("Redis/Hash.class.php");
include("Redis/Listing.class.php");
include("Redis/Sequence.class.php");
include("Redis/Subscription.class.php");
include("Redis/HLL.class.php");
include("Redis/Transaction.class.php");

use Origin\Kernel\Data\Redis\Key;
use Origin\Kernel\Data\Redis\Str;
use Origin\Kernel\Data\Redis\Set;
use Origin\Kernel\Data\Redis\Hash;
use Origin\Kernel\Data\Redis\Listing;
use Origin\Kernel\Data\Redis\Sequence;
use Origin\Kernel\Data\Redis\Subscription;
use Origin\Kernel\Data\Redis\HLL;
use Origin\Kernel\Data\Redis\Transaction;

class Redis
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
     * @var mixed $_Value
     * 被索引键值内容信息
    */
    protected $_Value = null;
    # 构造函数
    /**
     * @access public
     * @param string $connect_name 配置源名称
    */
    function __construct($connect_name=null)
    {
        $_connect_config = Config('DATA_MATRIX_CONFIG');
        if(is_array($_connect_config)){
            for($_i = 0;$_i < count($_connect_config);$_i++){
                # 判断数据库类型
                if(key_exists("DATA_TYPE",$_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "redis"
                    and key_exists("DATA_NAME",$_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name){
                    $_connect_conf = $_connect_config[$_i];
                    break;
                }
            }
            if(!isset($_connect_conf)) {
                for ($_i = 0; $_i < count($_connect_config); $_i++) {
                    # 判断数据库对象
                    if (key_exists("DATA_TYPE",$_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "redis") {
                        $_connect_conf = $_connect_config[$_i];
                        break;
                    }
                }
            }
            if (isset($_connect_conf)) {
                try{
                    # 创建数据库链接地址，端口，应用数据库信息变量
                    $_redis_host = strtolower(trim($_connect_conf['DATA_HOST']));
                    $_redis_port = intval(strtolower(trim($_connect_conf['DATA_PORT'])))?intval(strtolower(trim($_connect_conf['DATA_PORT']))):6379;
                    $this->_Connect = new \Redis();
                    if($_connect_conf['DATA_P_CONNECT']){
                        $this->_Connect->pconnect($_redis_host,$_redis_port);
                    }else{
                        $this->_Connect->connect($_redis_host,$_redis_port);
                    }
                    if(!is_null($_connect_conf['DATA_PWD']) and !empty($_connect_conf['DATA_PWD'])){
                        $this->_Connect->auth($_connect_conf['DATA_PWD']);
                    }
                }catch(\Exception $e){
                    var_dump(debug_backtrace(0,1));
                    echo("<br />");
                    print('Error:'.$e->getMessage());
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

    function key()
    {
        $_redis = new Key($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function string()
    {
        $_redis = new Str($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function set()
    {
        $_redis = new Set($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function hash()
    {
        $_redis = new Hash($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function listing()
    {
        $_redis = new Listing($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function seq()
    {
        $_redis = new Sequence($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function sub()
    {
        $_redis = new Subscription($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function hll()
    {
        $_redis = new HLL($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    function transaction()
    {
        $_redis = new Transaction($this->_Connect);
        $_redis->__setSQL($_redis);
        return $_redis;
    }
    /**
     * EVAL 使用 Lua 解释器执行脚本
     * Evalsha 给定的 sha1 校验码，执行缓存在服务器中的脚本
     * Script Exists 校验指定的脚本是否已经被保存在缓存当中
     * Script Load 脚本 script 添加到脚本缓存中，但并不立即执行这个脚本
     * Script Flush 清除所有 Lua 脚本缓存
     * Script kill 杀死当前正在运行的 Lua 脚本，当且仅当这个脚本没有执行过任何写操作时，这个命令才生效
     * 在结构实际应用中转化后函数结构过于繁琐，所以在实例应用中，直接使用该函数直接应用
     */
    /**
     * 执行Redis刷新
     * @access public
     * @param string $obj 刷新对象 all or db
     * @return object
    */
    function flush($obj="all")
    {
        try{
            if($obj == "db" or $obj == 1){
                $this->_Value = $this->_Connect->flushDB();
            }else{
                $this->_Value = $this->_Connect->flushAll();
            }
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * Echo 打印给定的字符串
     * Ping 客户端向 Redis 服务器发送一个 PING
     * Quit 关闭与当前客户端与redis服务的连接
     * 在结构实际应用中转化后函数结构过于繁琐，所以在实例应用中，直接使用该函数直接应用
     */
    /**
     * Select 切换到指定的数据库，数据库索引号 index 用数字值指定，以 0 作为起始索引值
     * @access public
     * @param int $db 指定数据库标尺
     * @return object
    */
    function selectDB($db)
    {
        try{
            $this->_Value = $this->_Connect->select($db);
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * Client Kill 关闭客户端连接
     * Client List 返回所有连接到服务器的客户端信息和统计数据
     * Client Getname 返回 CLIENT SETNAME 命令为连接设置的名字。 因为新创建的连接默认是没有名字的，
     * 对于没有名字的连接， CLIENT GETNAME 返回空白回复
     * Client Pause 阻塞客户端命令一段时间（以毫秒计）
     * Client Setname 指定当前连接的名称
     * Cluster Slots 当前的集群状态，以数组形式展示
     * Command 返回所有的Redis命令的详细信息，以数组形式展示
     * Command Count 统计 redis 命令的个数
     * Command Getkeys 获取所有 key
     * Command Info 获取 redis 命令的描述信息
     * Config Get 获取 redis 服务的配置参数
     * Config rewrite 启动 Redis 服务器时所指定的 redis.conf 配置文件进行改写
     * Config Set 态地调整 Redis 服务器的配置(configuration)而无须重启
     * Config Resetstat 重置 INFO 命令中的某些统计数据，包括：
                        Keyspace hits (键空间命中次数)
                        Keyspace misses (键空间不命中次数)
                        Number of commands processed (执行命令的次数)
                        Number of connections received (连接服务器的次数)
                        Number of expired keys (过期key的数量)
                        Number of rejected connections (被拒绝的连接数量)
                        Latest fork(2) time(最后执行 fork(2) 的时间)
                        The aof_delayed_fsync counter(aof_delayed_fsync 计数器的值)
     * Debug Object 调试命令，它不应被客户端所使用
     * Debug Segfault 非法的内存访问从而让 Redis 崩溃，仅在开发时用于 BUG 调试
     * Info 易于理解和阅读的格式，返回关于 Redis 服务器的各种信息和统计数值
     * server : 一般 Redis 服务器信息，包含以下域：
                redis_version : Redis 服务器版本
                redis_git_sha1 : Git SHA1
                redis_git_dirty : Git dirty flag
                os : Redis 服务器的宿主操作系统
                arch_bits : 架构（32 或 64 位）
                multiplexing_api : Redis 所使用的事件处理机制
                gcc_version : 编译 Redis 时所使用的 GCC 版本
                process_id : 服务器进程的 PID
                run_id : Redis 服务器的随机标识符（用于 Sentinel 和集群）
                tcp_port : TCP/IP 监听端口
                uptime_in_seconds : 自 Redis 服务器启动以来，经过的秒数
                uptime_in_days : 自 Redis 服务器启动以来，经过的天数
                lru_clock : 以分钟为单位进行自增的时钟，用于 LRU 管理
                clients : 已连接客户端信息，包含以下域：
                connected_clients : 已连接客户端的数量（不包括通过从属服务器连接的客户端）
                client_longest_output_list : 当前连接的客户端当中，最长的输出列表
                client_longest_input_buf : 当前连接的客户端当中，最大输入缓存
                blocked_clients : 正在等待阻塞命令（BLPOP、BRPOP、BRPOPLPUSH）的客户端的数量
                memory : 内存信息，包含以下域：
                used_memory : 由 Redis 分配器分配的内存总量，以字节（byte）为单位
                used_memory_human : 以人类可读的格式返回 Redis 分配的内存总量
                used_memory_rss : 从操作系统的角度，返回 Redis 已分配的内存总量（俗称常驻集大小）。这个值和 top 、 ps 等命令的输出一致。
                used_memory_peak : Redis 的内存消耗峰值（以字节为单位）
                used_memory_peak_human : 以人类可读的格式返回 Redis 的内存消耗峰值
                used_memory_lua : Lua 引擎所使用的内存大小（以字节为单位）
                mem_fragmentation_ratio : used_memory_rss 和 used_memory 之间的比率
                mem_allocator : 在编译时指定的， Redis 所使用的内存分配器。可以是 libc 、 jemalloc 或者 tcmalloc 。
                在理想情况下， used_memory_rss 的值应该只比 used_memory 稍微高一点儿。
                当 rss > used ，且两者的值相差较大时，表示存在（内部或外部的）内存碎片。
                内存碎片的比率可以通过 mem_fragmentation_ratio 的值看出。
                当 used > rss 时，表示 Redis 的部分内存被操作系统换出到交换空间了，在这种情况下，操作可能会产生明显的延迟。
                当 Redis 释放内存时，分配器可能会，也可能不会，将内存返还给操作系统。
                如果 Redis 释放了内存，却没有将内存返还给操作系统，那么 used_memory 的值可能和操作系统显示的 Redis 内存占用并不一致。
                查看 used_memory_peak 的值可以验证这种情况是否发生。
                persistence : RDB 和 AOF 的相关信息
                stats : 一般统计信息
                replication : 主/从复制信息
                cpu : CPU 计算量统计信息
                commandstats : Redis 命令统计信息
                cluster : Redis 集群信息
                keyspace : 数据库相关的统计信息
     * Monitor 实时打印出 Redis 服务器接收到的命令，调试用
     * Role 查看主从实例所属的角色，角色有master, slave, sentinel
     * Shutdown 执行以下操作：
                    停止所有客户端
                    如果有至少一个保存点在等待，执行 SAVE 命令
                    如果 AOF 选项被打开，更新 AOF 文件
                    关闭 redis 服务器(server)
     * Slaveof 当前服务器转变为指定服务器的从属服务器(slave server)
     * slowlog 是 Redis 用来记录查询执行时间的日志系统
     * Sync 命令用于同步主从服务器
     */
    /**
     * 最近一次 Redis 成功将数据保存到磁盘上的时间，以 UNIX 时间戳格式表示
     * @access public
     * @return object
    */
    function saveTime()
    {
        try{
            $this->_Value = $this->_Connect->lastSave();
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }

    /**
     * 返回redis服务器时间
     * @access public
     * @return object
    */
    function time()
    {
        try{
            $this->_Value = $this->_Connect->time();
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回数据库容量使用信息
     * @access public
     * @return object
    */
    function dbSize()
    {
        try{
            $this->_Value = $this->_Connect->dbSize();
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 异步执行一个 AOF（AppendOnly File） 文件重写操作
     * @access public
     * @return object
    */
    function bgAOF()
    {
        try{
            $this->_Value = $this->_Connect->bgrewriteaof();
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 异步保存当前数据库的数据到磁盘
     * @access public
     * @return object
    */
    function bgSave()
    {
        try{
            $this->_Value = $this->_Connect->bgsave();
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     *
     * @access public
     * @return object
     */
    function save()
    {
        try{
            $this->_Value = $this->_Connect->save();
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取当前操作所获取内容，或执行对象内容
     * @access public
     * @return mixed
    */
    function value()
    {
        return $this->_Value;
    }
    /**
     * 获取redis服务链接对象
     * @return object
    */
    function object()
    {
        return $this->_Connect;
    }
    /**
     * 析构函数
    */
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if(!is_null($this->_Connect))
            $this->_Connect = null;
    }
}