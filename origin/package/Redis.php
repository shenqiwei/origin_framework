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
     * @access protected
     * @var Redis|object $Connect 数据库链接对象
    */
    protected $Connect;

    /**
     * 构造函数，预加载数据源配置信息
     * @access public
     * @param string|null $connect_name 配置源名称
     * @return void
    */
    function __construct($connect_name=null)
    {
        $connect_config = config('DATA_MATRIX_CONFIG');
        if(is_array($connect_config)){
            for($i = 0;$i < count($connect_config);$i++){
                # 判断数据库类型
                if(key_exists("DATA_TYPE",$connect_config[$i]) and strtolower(trim($connect_config[$i]["DATA_TYPE"])) === "redis"
                    and key_exists("DATA_NAME",$connect_config[$i]) and $connect_config[$i]['DATA_NAME'] === $connect_name){
                    $connect_conf = $connect_config[$i];
                    break;
                }
            }
            if(!isset($connect_conf)) {
                for ($i = 0; $i < count($connect_config); $i++) {
                    # 判断数据库对象
                    if (key_exists("DATA_TYPE",$connect_config[$i]) and strtolower(trim($connect_config[$i]["DATA_TYPE"])) === "redis") {
                        $connect_conf = $connect_config[$i];
                        break;
                    }
                }
            }else
                $connect_config = $connect_conf;
            # 创建数据库链接地址，端口，应用数据库信息变量
            $host = strtolower(trim($connect_config['DATA_HOST']));
            $port = intval(strtolower(trim($connect_config['DATA_PORT'])))?intval(trim($connect_config['DATA_PORT'])):6379;
            $this->Connect = new \Redis();
            if($connect_config['DATA_P_CONNECT'])
                $this->Connect->pconnect($host,$port);
            else
                $this->Connect->connect($host,$port);
            if(!is_null($connect_config['DATA_PWD']) and !empty($connect_conf['DATA_PWD']))
                $this->Connect->auth($connect_conf['DATA_PWD']);
        }
    }

    /**
     * 调用键位功能封装
     * @access public
     * @return object 数据源连接对象
    */
    function key()
    {
        return new Key($this->Connect);
    }

    /**
     * 调用字符串功能封装
     * @access public
     * @return object 数据源连接对象
     */
    function string()
    {
        return new Str($this->Connect);
    }

    /**
     * 调用集合包功能封装
     * @access public
     * @return object 数据源连接对象
     */
    function set()
    {
        return new Set($this->Connect);
    }

    /**
     * 调用哈希表功能封装
     * @access public
     * @return object 数据源连接对象
     */
    function hash()
    {
        return new Hash($this->Connect);
    }

    /**
     * 调用列表功能包封装
     * @access public
     * @return object 数据源连接对象
     */
    function lists()
    {
        return new Lists($this->Connect);
    }

    /**
     * 调用队列表功能函数封装
     * @access public
     * @return object 数据源连接对象
     */
    function seq()
    {
        return new Sequence($this->Connect);
    }

    /**
     * 执行Redis刷新
     * @access public
     * @param string $obj 刷新对象 all or db
     * @return boolean 返回执行结果状态值
    */
    function flush($obj="all")
    {
        if($obj == "db" or $obj == 1){
            $receipt = $this->Connect->flushDB();
        }else{
            $receipt = $this->Connect->flushAll();
        }
        return $receipt;
    }

    /**
     * Select 切换到指定的数据库，数据库索引号 index 用数字值指定，以 0 作为起始索引值
     * @access public
     * @param int $db 指定数据库标尺
     * @return bool 返回执行结果
    */
    function selectDB($db)
    {
        return $this->Connect->select($db);
    }

    /**
     * 最近一次 Redis 成功将数据保存到磁盘上的时间，以 UNIX 时间戳格式表示
     * @access public
     * @return int 返回时间戳
    */
    function saveTime()
    {
        return $this->Connect->lastSave();
    }

    /**
     * 返回redis服务器时间
     * @access public
     * @return array 返回服务时间
    */
    function time()
    {
        return $this->Connect->time();
    }

    /**
     * 返回数据库容量使用信息
     * @access public
     * @return int 返回数据库可用容量
    */
    function dbSize()
    {
        return $this->Connect->dbSize();
    }

    /**
     * 异步执行一个 AOF（AppendOnly File） 文件重写操作
     * @access public
     * @return bool 返回执行结果状态值
    */
    function bgAOF()
    {
        return $this->Connect->bgrewriteaof();
    }

    /**
     * 异步保存当前数据库的数据到磁盘
     * @access public
     * @return bool 返回执行结果状态值
    */
    function bgSave()
    {
        return $this->Connect->bgsave();
    }

    /**
     * 保存当前数据库的数据到磁盘
     * @access public
     * @return bool 返回执行结果状态值
     */
    function save()
    {
        return $this->Connect->save();
    }

    /**
     * 析构函数，释放连接
     * @access public
     * @return void
    */
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if(!is_null($this->Connect))
            $this->Connect = null;
    }
}