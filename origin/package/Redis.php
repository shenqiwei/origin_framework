<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package;

use Origin\Package\Redis\Key;
use Origin\Package\Redis\Str;
use Origin\Package\Redis\Set;
use Origin\Package\Redis\Hash;
use Origin\Package\Redis\Lists;
use Origin\Package\Redis\Sequence;

class Redis
{
    /**
     * @var object $_Connect 数据库链接对象
    */
    private $Connect = null;
    # 构造函数
    /**
     * @access public
     * @param string $connect_name 配置源名称
    */
    function __construct($connect_name=null)
    {
        $_connect_config = config('DATA_MATRIX_CONFIG');
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
            }else
                $_connect_config = $_connect_conf;
            # 创建数据库链接地址，端口，应用数据库信息变量
            $_redis_host = strtolower(trim($_connect_config['DATA_HOST']));
            $_redis_port = intval(strtolower(trim($_connect_config['DATA_PORT'])))?intval(strtolower(trim($_connect_config['DATA_PORT']))):6379;
            $this->Connect = new \Redis();
            if($_connect_config['DATA_P_CONNECT'])
                $this->Connect->pconnect($_redis_host,$_redis_port);
            else
                $this->Connect->connect($_redis_host,$_redis_port);
            if(!is_null($_connect_config['DATA_PWD']) and !empty($_connect_conf['DATA_PWD']))
                $this->Connect->auth($_connect_conf['DATA_PWD']);
        }
    }

    function key()
    {
        return new Key($this->Connect);
    }
    function string()
    {
        return new Str($this->Connect);
    }
    function set()
    {
        return new Set($this->Connect);
    }
    function hash()
    {
        return new Hash($this->Connect);
    }
    function lists()
    {
        return new Lists($this->Connect);
    }
    function seq()
    {
        return new Sequence($this->Connect);
    }
    /**
     * 执行Redis刷新
     * @access public
     * @param string $obj 刷新对象 all or db
     * @return bool
    */
    function flush($obj="all")
    {
        if($obj == "db" or $obj == 1){
            $_receipt = $this->Connect->flushDB();
        }else{
            $_receipt = $this->Connect->flushAll();
        }
        return $_receipt;
    }
    /**
     * Select 切换到指定的数据库，数据库索引号 index 用数字值指定，以 0 作为起始索引值
     * @access public
     * @param int $db 指定数据库标尺
     * @return bool
    */
    function selectDB($db)
    {
        return $this->Connect->select($db);
    }
    /**
     * 最近一次 Redis 成功将数据保存到磁盘上的时间，以 UNIX 时间戳格式表示
     * @access public
     * @return int
    */
    function saveTime()
    {
        return $this->Connect->lastSave();
    }

    /**
     * 返回redis服务器时间
     * @access public
     * @return array
    */
    function time()
    {
        return $this->Connect->time();
    }
    /**
     * 返回数据库容量使用信息
     * @access public
     * @return int
    */
    function dbSize()
    {
        return $this->Connect->dbSize();
    }
    /**
     * 异步执行一个 AOF（AppendOnly File） 文件重写操作
     * @access public
     * @return bool
    */
    function bgAOF()
    {
        return $this->Connect->bgrewriteaof();
    }
    /**
     * 异步保存当前数据库的数据到磁盘
     * @access public
     * @return bool
    */
    function bgSave()
    {
        return $this->Connect->bgsave();
    }
    /**
     * 保存当前数据库的数据到磁盘
     * @access public
     * @return bool
     */
    function save()
    {
        return $this->Connect->save();
    }
    /**
     * 析构函数
    */
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if(!is_null($this->Connect))
            $this->Connect = null;
    }
}