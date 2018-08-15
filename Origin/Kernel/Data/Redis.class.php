<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Data.Redis *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2018/08/09 15:03
 * update Time: 2017/08/09 15:03
 * chinese Context: IoC Redis封装类
 */
namespace Origin\Kernel\Data;

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
        if(is_null($connect_name)){
            try{
                # 创建数据库链接地址，端口，应用数据库信息变量
                $_redis_host = Config('DATA_HOST');
                $_redis_port = intval(Config('DATA_PORT'))?intval(Config("DATA_PORT")):6379;
                $this->_Connect = new \Redis();
                if(Config('DATA_P_CONNECT')){
                    $this->_Connect->pconnect($_redis_host,$_redis_port);
                }else{
                    $this->_Connect->connect($_redis_host,$_redis_port);
                }
                if(!is_null(Config('DATA_PWD')) and !empty(Config('DATA_PWD'))){
                    $this->_Connect->auth(Config('DATA_PWD'));
                }
            }catch(\Exception $e){
                var_dump(debug_backtrace(0,1));
                echo("<br />");
                print('Error:'.$e->getMessage());
                exit();
            }
        }else{
            $_connect_config = Config('DATA_MATRIX_CONFIG');
            if(is_array($_connect_config)){
                for($_i = 0;$_i < count($_connect_config);$_i++){
                    # 判断数据库类型，框架结构默认系统类型为mysql
                    if(key_exists("DATA_TYPE",$_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "redis"){
                        # 搜索指向配置信息名称
                        if(key_exists("DATA_NAME",$_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name){
                            try{
                                # 创建数据库链接地址，端口，应用数据库信息变量
                                $_redis_host = strtolower(trim($_connect_config[$_i]['DATA_HOST']));
                                $_redis_port = intval(strtolower(trim($_connect_config[$_i]['DATA_PORT'])))?intval(strtolower(trim($_connect_config[$_i]['DATA_PORT']))):6379;
                                $this->_Connect = new \Redis();
                                if($_connect_config[$_i]['DATA_P_CONNECT']){
                                    $this->_Connect->pconnect($_redis_host,$_redis_port);
                                }else{
                                    $this->_Connect->connect($_redis_host,$_redis_port);
                                }
                                if(!is_null($_connect_config[$_i]['DATA_PWD']) and !empty($_connect_config[$_i]['DATA_PWD'])){
                                    $this->_Connect->auth($_connect_config[$_i]['DATA_PWD']);
                                }
                            }catch(\Exception $e){
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
     * 创建元素对象值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
    */
    function strCreate($key,$value){
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->set($key,$value);
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}";
            }else{
                # 回写冲突对象内容
                $this->_Value = "{$key}:".$this->_Connect->get($key);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param string $columns 被创建元素对象列表
     *
    */
    /**
     * 创建元素对象，并设置生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $second 生命周期时间（second）
     * @return object
     */
    function strCreateSec($key,$value,$second=0)
    {
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->setex($key,$value,intval($second));
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}, cycle:{$second} seconds";
            }else{
                # 回写冲突对象内容
                $this->_Value = "{$key}:".$this->_Connect->get($key);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 非覆盖创建元素对象值
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
    */
    function strCreateNE($key,$value)
    {
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->setnx($key,$value);
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}";
            }else{
                # 回写冲突对象内容
                $this->_Value = "ERROR_KEY_IS_ALREADY_EXISTS";
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 创建元素对象并，设置生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $milli 生命周期时间（milli）
     * @return object
     */
    function strCreateMil($key,$value,$milli=0)
    {
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->psetex($key,$value,intval($milli));
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}, cycle:{$milli} milliseconds";
            }else{
                # 回写冲突对象内容
                $this->_Value = "{$key}:".$this->_Connect->get($key);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 覆盖原创建元素对象值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
    */
    function strReCreate($key,$value){
        try{
            # 创建对象元素内容并赋值
            $this->_Connect->set($key,$value);
            # 回写写入对象元素内容
            $this->_Value = "{$key}:{$value}";
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 设置元素对象偏移值
     * @access public
     * @param string $key 被创建对象键名
     * @param int $offset 被创建元素对象内容值
     * @param int $value 偏移系数
     * @return object
    */
    function strCBit($key,$offset,$value)
    {
        try{
            if($this->_Connect->exists($key)){

                $this->_Value = $this->_Connect->setBit($key,$offset,$value);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }else{
                $this->_Value = "ERROR_NOT_HAS_KEY_OBJECT";
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 叠加（创建）对象元素值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
    */
    function strAppend($key,$value)
    {
        try{
            # 叠加（创建）对象元素内容并赋值
            $this->_Connect->append($key,$value);
            # 回写写入对象元素内容
            $this->_Value = "{$key}:".$this->_Connect->get($key);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 检索元素对象值内容
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function strGet($key){
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->get($key);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取元素对象偏移值
     * @access public
     * @param string $key 被检索对象键名
     * @param int $offset 被创建元素对象内容值
     * @return object
    */
    function strGBit($key,$offset)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->getBit($key,$offset);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 检索元素对象值内容长度
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function strGetLen($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->strlen($key);
            }else{
                $this->_Value = 0;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 检索元素对象值（区间截取）内容，（大于0的整数从左开始执行，小于0的整数从右开始执行）
     * @access public
     * @param string $key 被检索对象键名
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return object
    */
    function strGetRange($key,$start=1,$end=-1)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->getRange($key,$start,$end);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 检索元素对象进行初始化内容
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
     */
    function strGetRollback($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->getSet($key,$value);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return object
    */
    function strCreateList($columns)
    {
        try{
            if(is_array($columns)){
                $this->_Connect->mset($columns);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 非替换创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return object
    */
    function strCreateLNE($columns)
    {
        try{
            if(is_array($columns)){
                $this->_Connect->msetnx($columns);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 检索元素列表
     * @access public
     * @param array $keys 对应元素列表数组
     * @return object
     */
    function strGetList($keys)
    {
        try{
            if(is_array($keys)){
                $this->_Value = $this->_Connect->mget($keys);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 对应元素（数据）指定值自增
     * @access public
     * @param string $key 被检索对象键名
     * @param int $increment 自增系数值
     * @return object
    */
    function strPlus($key,$increment=1)
    {
        try{
            # 判断执行元素对象是否为数字
            if($this->_Connect->exists($key) and is_numeric($this->_Connect->get($key))){
                # 判断系数条件是否为大于的参数值
                if(intval($increment) > 1){
                    if(is_int($increment)){
                        # 执行自定义递增操作
                        $this->_Connect->incrBy($key,intval($increment));
                    }else{
                        # 执行自定义递增(float,double)操作
                        $this->_Connect->incrByFloat($key,floatval($increment));
                    }
                }else{
                    # 执行递增1操作
                    $this->_Connect->incr($key);
                }
                $this->_Value = $this->_Connect->get($key);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 对应元素（数据）指定值自减
     * @access public
     * @param string $key 被检索对象键名
     * @param int $decrement 自减系数值
     * @return object
     */
    function strMinus($key,$decrement=1)
    {
        try{
            # 判断执行元素对象是否为数字
            if($this->_Connect->exists($key) and is_numeric($this->_Connect->get($key))){
                # 判断系数条件是否为大于的参数值
                if(intval($decrement) > 1){
                    # 执行自定义递减操作
                    $this->_Connect->decrBy($key,intval($decrement));
                }else{
                    # 执行递减1操作
                    $this->_Connect->decr($key);
                }
                $this->_Value = $this->_Connect->get($key);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 删除元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return object
    */
    function keyDel($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->get($key);
                if($this->_Value === "nil")
                    $this->_Value = null;
                $this->_Connect->delete($key);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列化元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return object
    */
    function keyDump($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->dump($key);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用时间戳设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return object
    */
    function keySetTSC($key,$timestamp)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->expireAt($key,$timestamp);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用秒计时单位设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $second 时间戳
     * @return object
    */
    function keySetSec($key,$second)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->expire($key,$second);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用毫秒时间戳设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return object
     */
    function keySetTSM($key,$timestamp)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->pExpireAt($key,$timestamp);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用毫秒计时单位设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $millisecond 时间戳
     * @return object
     */
    function keySetMil($key,$millisecond)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->pExpire($key,$millisecond);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除元素目标生命周期限制
     * @access public
     * @param string $key 被检索对象键名
     * @return object
    */
    function keyRmCycle($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->persist($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取元素对象剩余周期时间(毫秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return object
    */
    function pTTL($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->pttl($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取元素对象剩余周期时间(秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function TTL($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->ttl($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取搜索相近元素对象键
     * @access public
     * @param string $closeKey 相近元素对象（key*）
     * @return object
    */
    function keys($closeKey)
    {
        try{
            $this->_Value = $this->_Connect->keys($closeKey);
            if($this->_Value === "nil")
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
     * 随机返回元素键
     * @access public
     * @return object
    */
    function randKey()
    {
        try{
            $this->_Value = $this->_Connect->randomKey();
            if($this->_Value === "nil")
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
     * 重命名元素对象
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return object
    */
    function rnKey($key,$newKey)
    {
        try {
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->rename($key, $newKey);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }else{
                $this->_Value = "ERROR_NOT_HAS_KEY_OBJECT";
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 非重名元素对象重命名
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return object
    */
    function irnKey($key,$newKey)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->renameNx($key, $newKey);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }else{
                $this->_Value = "ERROR_NOT_HAS_KEY_OBJECT";
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取元素对象内容数据类型
     * @access public
     * @param string $key 被检索对象键名
     * @return object
    */
    function type($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->type($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 将元素对象存入数据库
     * @access public
     * @param string $key 被检索对象键名
     * @param string $database 对象数据库名
     * @return object
    */
    function inDB($key,$database)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->move($key, $database);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return object
    */
    function hashCreate($key,$field,$value)
    {
        try{
            if (!$this->_Connect->hExists($key, $field)) {
                $this->_Value = $this->_Connect->hSet($key, $field, $value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 覆盖创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return object
     */
    function reHashCreate($key,$field,$value)
    {
        try{
            $this->_Value = $this->_Connect->hSet($key, $field, $value);
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
     * @access public
     * @param string $key 创建对象元素键
     * @param array $array 字段数组列表
     * @return object
     */
    function hashCreateList($key,$array)
    {
        try {
            if (is_array($array)) {
                $this->_Value = $this->_Connect->hMset($key,$array);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 非替换创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return object
     */
    function hashCreateNE($key,$field,$value)
    {
        try{
            $this->_Value = $this->_Connect->hSetNx($key,$field,$value);
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
     * 获取hash元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return object
    */
    function hashGet($key,$field)
    {
        try{
            if($this->_Connect->exists($key)) {
                if ($this->_Connect->hExists($key, $field)) {
                    $this->_Value = $this->_Connect->hGet($key, $field);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
                }
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回hash元素对象列表
     * @access public
     * @param string $key 索引对象元素键
     * @return object
    */
    function hashList($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hGetAll($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取hash元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param array $array 字段数组列表
     * @return object
    */
    function hashGetList($key,$array)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hMGet($key,$array);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取hash元素对象区间列表内容(用于redis翻页功能)
     * @access public
     * @param string $key 索引对象元素键
     * @param int $start 起始位置标记
     * @param string $pattern 执行模板(搜索模板)
     * @param int $count 显示总数
     * @return object
     */
    function hashLimit($key,$start,$pattern,$count)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hScan($key,$start,$pattern,$count);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回hash元素对象列表
     * @access public
     * @param string $key 索引对象元素键
     * @return object
     */
    function hashValues($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hVals($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 删除元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return object
    */
    function hashDel($key,$field)
    {
        try{
            if($this->_Connect->exists($key)){
                if($this->_Connect->hExists($key,$field)) {
                    $this->_Value = $this->_Connect->hDel($key,$field);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
                }
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 设置hash元素对象增量值
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @param int $value 增量值
     * @return object
    */
    function hashPlus($key,$field,$value)
    {
        try{
            if($this->_Connect->exists($key)) {
                if ($this->_Connect->hExists($key, $field)) {
                    if (is_float($value)) {
                        $this->_Value = $this->_Connect->hIncrByFloat($key, $field, $value);
                    } else {
                        $this->_Value = $this->_Connect->hIncrBy($key, $field, intval($value));
                    }
                    if ($this->_Value === "nil")
                        $this->_Value = null;
                }
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取hash元素对象全部字段名(域)
     * @access public
     * @param string $key 索引元素对象键
     * @return object
    */
    function hashFields($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hKeys($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取hash元素对象字段内容（域）长度
     * @access public
     * @param string $key 索引元素对象键s
     * @return object
    */
    function hashLen($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hLen($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**s
     * 移出并获取列表的第一个元素
     * @access public
     * @param array $keys 索引元素对象列表
     * @param int $time 最大等待时长
     * @return object
    */
    function removeFirst($keys,$time)
    {
        try{
            if(is_array($keys)) {
                $this->_Value = $this->_Connect->blPop($keys,$time);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取列表的最后一个元素
     * @access public
     * @param array $keys 索引元素对象列表
     * @param int $time 最大等待时长
     * @return object
    */
    function removeLast($keys,$time)
    {
        try{
            if(is_array($keys)) {
                $this->_Value = $this->_Connect->brPop($keys,$time);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 抽取元素对象值内容，转存至目标元素对象中
     * @access public
     * @param string $key 索引元素对象键
     * @param string $write 转存目标对象键
     * @param int $time 最大等待时长
     * @return object
    */
    function reIn($key,$write,$time)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->brpoplpush($key,$write,$time);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 索引元素对象，并返回内容信息（大于0从左开始，小于0从右侧开始）
     * @access public
     * @param string $key 索引元素对象键
     * @param int $index 索引位置参数
     * @return object
    */
    function index($key,$index){
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->lIndex($key,$index);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 在列表的元素前或者后插入元素
     * @access public
     * @param string $key 索引元素对象键
     * @param string $be 插入位置
     * @param mixed $value 目标元素值
     * @param mixed $write 写入值
     * @return object
    */
    function insert($key,$be="after",$value,$write)
    {
        try{
            if($this->_Connect->exists($key)) {
                if($be === "before"){
                    $_location = 0;
                }else{
                    $_location = 1;
                }
                $this->_Value = $this->_Connect->lInsert($key,$_location,$value,$write);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回列表的长度
     * @access public
     * @param string $key 索引元素对象键
     * @return object
    */
    function count($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = intval($this->_Connect->lLen($key));
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除并返回列表的第一个元素
     * @access public
     * @param string $key 索引元素对象键
     * @return object
    */
    function popFirst($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->lPop($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除并返回列表的最后一个元素
     * @access public
     * @param string $key 索引元素对象键
     * @return object
    */
    function popLast($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->rPop($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 将元素对象列表的最后一个元素移除并返回，并将该元素添加到另一个列表
     * @access public
     * @param string $key
     * @param string $write
     * @return object
    */
    function popWrite($key,$write)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->rpoplpush($key,$write);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 在列表头部插入一个或多个值
     * @access public
     * @param string $key 索引元素对象键
     * @param  mixed $value 插入对象值
     * @return object
    */
    function inFirst($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->lPush($key,$value);
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
     * 在列表尾部插入一个或多个值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return object
    */
    function inLast($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->rPush($key,$value);
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
     * 在已存在的列表头部插入一个值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return object
     */
    function inFFirst($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lPushx($key,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 在已存在的列表尾部插入一个值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return object
     */
    function inFLast($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->rPushx($key,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回列表中指定区间内的元素
     * @access public
     * @param string $key 索引元素对象键
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return object
    */
    function range($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lRange($key,$start,$end);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 根据参数 COUNT 的值，移除列表中与参数 VALUE 相等的元素
     * @access public
     * @param string $key 索引元素对象键
     * @param int $count 执行(总数)系数 (count > 0: 从表头开始向表尾搜索,count < 0:从表尾开始向表头搜索，count = 0: 删除所有与value相同的)
     * @param mixed $value 操作值
     * @return object
    */
    function rem($key,$count,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lRem($key,$count,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 设置索引元素对象
     * @access public
     * @param string $key 索引元素对象键
     * @param int $index 索引系数
     * @param mixed $value 设置值
     * @return object
    */
    function indexSet($key,$index,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lSet($key,$index,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 保留指定区间内的元素
     * @access public
     * @param string $key 索引元素对象键
     * @param int $start 起始位置系数
     * @param int $end 结束位置系数
     * @return object
    */
    function trim($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lTrim($key,$start,$end);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 集合：向集合添加一个或多个成员
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 存入值
     * @return object
    */
    function setAdd($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->sAdd($key,$value);
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
     * 获取集合内元素数量
     * @access public
     * @param string $key 索引元素对象键
     * @return object
    */
    function setCount($key)
    {
        try{
            $this->_Value = $this->_Connect->sCard($key);
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
     * 获取两集合差值
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 比对元素对象键
     * @return object
    */
    function setDiff($key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->sDiff($key,$second);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取两集合之间的差值，并存入新集合中
     * @access public
     * @param string $new 新集合元素对象
     * @param string $key 索引元素对象
     * @param string $second 比对元素对象键
     * @return object
    */
    function setDifferent($new=null,$key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                if(!is_null($new)){
                    $this->_Value = $this->_Connect->sDiffStore($new,$key,$second);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
                }else{
                    $this->_Value = "INVALID_COLUMN_OBJECT";
                }
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 判断集合元素对象值是否存在元素对象中
     * @access public
     * @param string $key 索引元素对象键
     * @param string $value 验证值
     * @return object
    */
    function setMember($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sIsMember($key,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回元素对象集合内容
     * @access public
     * @param string $key 索引元素对象键
     * @return object
    */
    function setReturn($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sMembers($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 元素对象集合值迁移至其他集合中
     * @param string $key 索引元素对象键
     * @param string $second 迁移集合对象
     * @param mixed $value 迁移值
     * @return object
    */
    function setMove($key,$second,$value)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                if(!is_null($value)){
                    $this->_Value = $this->_Connect->sMove($key,$second,$value);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
                }else{
                    $this->_Value = "INVALID_COLUMN_OBJECT";
                }
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除元素对象随机内容值
     * @access public
     * @param string $key 索引元素对象
     * @return object
    */
    function setPop($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sPop($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 随机从元素对象中抽取指定数量元素内容值
     * @access public
     * @param string $key 索引元素对象键
     * @param int $count 随机抽调数量
     * @return object
    */
    function setRandMember($key,$count=1)
    {
        try{
            if($this->_Connect->exists($key)){
                if($count > 1)
                    $this->_Value = $this->_Connect->sRandMember($key);
                else
                    $this->_Value = $this->_Connect->sRandMember($key,$count);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除元素对象中指定元素内容
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return object
    */
    function setRemove($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sRem($key,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回指定两个集合对象的并集
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return object
    */
    function setMerge($key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->sUnion($key,$second);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回指定两个集合对象的并集
     * @access public
     * @param string $new 存储指向集合键
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return object
     */
    function setMergeTo($new,$key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->sUnionStore($new,$key,$second);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 迭代元素对象指定结构内容
     * @access public
     * @param string $key 索引元素对象
     * @param int $cursor 执行标尺
     * @param string $pattern 操作参数
     * @param string $value 索引值
     * @return object
    */
    function setTree($key,$cursor=0,$pattern="match",$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sScan($key,$cursor,$pattern,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列增加元素对象内容值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $param 标记
     * @param mixed $value 存入值
     * @return object
    */
    function seqAdd($key,$param,$value)
    {
        try{
            $this->_Value = $this->_Connect->zAdd($key,$param,$value);
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
     * 返回序列中元素对象内容数
     * @access public
     * @param string $key 索引元素对象键
     * @return object
    */
    function seqCount($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zCard($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列元素对象中区间值数量
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间数
     * @param string $max 最大区间数
     * @return object
    */
    function seqMMCount($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zCount($key,$min,$max);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列中元素对象值增加自增系数
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $increment 自增系数
     * @param mixed $value 值
     * @return object
    */
    function seqAi($key,$increment,$value)
    {
        try{
            $this->_Value = $this->_Connect->zIncrBy($key,$increment,$value);
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
     * 搜索两个序列指定系数成员内容，并存入新的序列中
     * @access public
     * @param string $new 目标序列键
     * @param string $key 索引元素对象键
     * @param mixed $param 索引系数
     * @param string $second 比对索引对象键
     * @return object
    */
    function seqDifferent($new,$key,$param,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->zInterStore($new,$key,$param,$second);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列中字典区间值数量
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间系数
     * @param string $max 最大区间系数
     * @return object
    */
    function seqDictCount($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zLexCount($key,$min,$max);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列元素对象指定区间内容对象内容
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return object
     */
    function seqRange($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRange($key,$min,$max);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列元素对象指定字典区间内容
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min
     * @param string $max
     * @return object
    */
    function seqDictRange($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRangeByLex($key,$min,$max);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列元素对象指定分数区间内容
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return object
    */
    function seqLimitRange($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRangeByScore($key,$min,$max);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回有序集合中指定成员的索引
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return object
    */
    function seqIndex($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRank($key,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除有序集合中的一个成员
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return object
    */
    function seqRemove($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRem($key,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除有序集合中给定的字典区间的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return object
    */
    function seqDictRemove($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRemRangeByLex($key,$start,$end);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除有序集中，指定排名(rank)区间内的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return object
    */
    function seqDictRank($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRemRangeByRank($key,$start,$end);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除有序集中，指定分数（score）区间内的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return object
    */
    function seqDictScore($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRemRangeByRank($key,$min,$max);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回有序集中，指定区间内的成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return object
    */
    function seqDescRange($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRevRange($key,$start,$end);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 返回有序集中，成员的分数值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return object
    */
    function seqScore($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zScore($key,$value);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * zUnionStore 计算给定的一个或多个有序集的并集，并存储在新的 key 中
     * zScan 迭代有序集合中的元素（包括元素成员和元素分值）
     * 在结构实际应用中转化后函数结构过于繁琐，所以在实例应用中，直接使用该函数直接应用
    */
    /**
     * 将元素对象参数添加到HyperLogLog 数据结构中
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 写入值
     * @return object
    */
    function HLLAdd($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->pfAdd($key,$value);
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
     *  计算HyperLogLog 的元素对象基数,或综合
     * @access public
     * @param mixed $key 索引元素对象键
     * @return object
    */
    function HLLCount($key)
    {
        try{
            if($this->_Connect->exists($key) or is_array($key)){
                $this->_Value = $this->_Connect->pfCount($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 多个 HyperLogLog 合并为一个 HyperLogLog
     * @access public
     * @param string $new 合成后HLL序列
     * @param array $key HLL原始序列集合
     * @return object
    */
    function HLLMerge($new,$key)
    {
        try{
            if(is_array($key)){
                $this->_Value = $this->_Connect->pfMerge($new,$key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 订阅一个或多个符合给定模式的频道
     * @access public
     * @param array $pattern 订阅频道参数数组
     * @param mixed $callback 回调参数对象
     * @return object
    */
    function subChannel($pattern,$callback)
    {
        try{
            $this->_Value = $this->_Connect->psubscribe($pattern,$callback);
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
     * 订阅与发布系统状态
     * @access public
     * @param string $command 子操作命令
     * @param mixed $argument 命令摘要
     * @return object
    */
    function subStatus($command,$argument)
    {
        try{
            $this->_Value = $this->_Connect->pubsub($command,$argument);
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
     * 信息发送到指定的频道
     * @access public
     * @param string $channel 对象频道
     * @param string $message 发送信息
     * @return object
    */
    function subMessage($channel,$message)
    {
        try{
            $this->_Value = $this->_Connect->publish($channel,$message);
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
     * 退订所有给定模式的频道
     * @access public
     * @param string $channel 退订频道
     * @return object
    */
    function unSubChannel($channel)
    {
        try{
            $this->_Value = $this->_Connect->punsubscribe($channel);
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
     * 订阅给定的一个或多个频道的信息
     * @access public
     * @param array $channel 订阅频道参数数组
     * @param mixed $callback 回调参数对象
     * @return object
    */
    function subMChannel($channel,$callback)
    {
        try{
            $this->_Value = $this->_Connect->subscribe($channel,$callback);
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
     * 退订给定的一个或多个频道的信息
     * @access public
     * @param array $channel
     * @return object
    */
    function unSubMChannel($channel)
    {
        try{
            $this->_Value = $this->_Connect->unsubscribe($channel);
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
     * 取消事务，放弃执行事务块内的所有命令
     * @access public
     * @return object
    */
    function tsDisable()
    {
        try{
            $this->_Value = $this->_Connect->discard();
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
     * 执行所有事务块内的命令
     * @access public
     * @return object
     */
    function tsExecute()
    {
        try{
            $this->_Value = $this->_Connect->exec();
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
     * 标记一个事务块的开始
     * @access public
     * @return object
     */
    function tsMulti()
    {
        try{
            $this->_Value = $this->_Connect->multi();
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
     * 取消 WATCH 命令对所有 key 的监视
     * @access public
     * @return object
     */
    function tsUnWatch()
    {
        try{
            $this->_Value = $this->_Connect->unwatch();
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
     * 监视一个(或多个) key
     * @access public
     * @param mixed $key 事务标记
     * @return object
     */
    function tsWatch($key)
    {
        try{
            $this->_Value = $this->_Connect->watch($key);
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