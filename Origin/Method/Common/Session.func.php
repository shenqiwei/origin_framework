<?php
/**
 * session会话操作函数
 * @access public
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function Session($key, $value=null)
{
    /**
     * @var string $_receipt
     * @var string $_validate_key
     * @var string $_validate_operate
     * @var mixed $_session
     * @var array $_operate
     * @var string $_resource
     * @var string $_set
     * @var array $_array_key
     */
    # 创建返回信息变量
    $_receipt = null;
    # 创建session键名过滤正则
    $_validate_key = '/^[^\_\W]+([\.\:\_\-]?[^\_\W]+)?$/';
    # 创建操作验证变量正则
    $_validate_operate = '/^(id|session|encode|reset|regenerate|destroy|unset|delete|encode|decode){1}$/';
    # 获取session配置列表
    $_session = Config('SESSION');
    # 进行验证，如果键名结构验证失败，则抛出异常
    if(is_true($_validate_key,$key) === true){
        # 判断php.ini是否设置了自动启用session会话,如果未开启，则启动session会话
        if(!ini_get('session.auto_start')) session_start();
        if(strpos($key, ':')){
            # 将字符串转为数组，根据php特性数组最小长度为 2
            $_operate = explode(':',strtolower($key));
            if(count($_operate)){
                if(count($_operate) > 3){
                    # 异常提示：超出程序最大执行范围
                    # 由于session设置及内建函数基本描述最大，使用3段语块格式可完全表达，
                    # 所以超出3段或者多块基本混淆测试，都将视为攻击或恶意操作
                    try{
                        throw new Exception('Origin Method Error: Is beyond the scope biggest execution');
                    }catch(Exception $e){
                        echo($e->getMessage());
                        exit();
                    }
                }
                # 判断session设置基本条件是否符合预设语法规则
                if($_operate[0] == 'session' and ((count($_operate) == 3
                            and (is_true($_validate_operate,$_operate[1] !== true)
                                and is_true($_validate_operate,$_operate[2]) !== true))
                        or (count($_operate) == 2 and is_true($_validate_operate,$_operate[1]) !== true))){
                    # 创建中间变量 resource
                    $_resource = null;
                    # 创建中间变量 set
                    $_set = null;
                    # 判断操作数组长度，
                    if(count($_operate) == 3){
                        # 判断设置项是否存在，如果不存在抛出异常
                        if(!array_key_exists($_operate[1], $_session) and !array_key_exists($_operate[2], $_session)){
                            # 异常提示：框架暂不支持该session设置项
                            try{
                                throw new Exception('Origin Method Error: The framework of short duration does not support the session Settings');
                            }catch(Exception $e){
                                echo($e->getMessage());
                                exit();
                            }
                        }else{
                            # 判断判断操作项参数位置，并赋入中间变量中
                            if(array_key_exists($_operate[2], $_session)){
                                $_resource = $_operate[1];
                                $_set = $_operate[2];
                            }else{
                                $_resource = $_operate[2];
                                $_set = $_operate[1];
                            }
                        }
                    }else{
                        $_set = $_operate[1];
                    }
                    # 判断设置信息方法
                    if($_resource !='php' and $_resource != 'config')
                        $_resource = 'auto';
                    # 当操作类型为set时，session将激活ini_set方法
                    if($value != null){
                        # 判断配置信息来源
                        if($_resource == 'config'){
                            if(array_key_exists(strtoupper($_set), $_session))
                                ini_set('session.'.strtolower($_set), $_session[strtoupper($_set)]);
                        }elseif($_resource == 'auto'){
                            if(array_key_exists(strtoupper($_set), $_session))
                                ini_set('session.'.strtolower($_set), $value);
                        }
                    }else{
                        if(array_key_exists(strtoupper($_set), $_session))
                            $_receipt = ini_get('session.'.strtolower($_set));
                    }
                }else{
                    $_set = null;
                    if($_operate[0] == 'session'){
                        if(is_true($_validate_operate, $_operate[1]) === true)
                            $_set = $_operate[1];
                    }else{
                        if(is_true($_validate_operate, $_operate[0]) === true)
                            $_set = $_operate[0];
                        else
                            $_set = $_operate[1];
                    }
                    # 获取session_id
                    if($_set == 'id') $_receipt = session_id();
                    # 注销session会话
                    if($_set == 'unset') session_unset();
                    # 清空session会话
                    if($_set == 'destroy') session_destroy();
                    # 重置session_id并保留原id
                    if($_set == 'regenerate') session_regenerate_id(false);
                    # 重置session内值
                    if($_set == 'reset') session_reset();
                    # 删除session的值
                    if($_set == 'delete'){
                        if(isset($_SESSION[$value])) unset($_SESSION[$value]);
                    }
                    # 编码session信息
                    if($_set == 'encode') session_encode();
                    # 解码session信息
                    if($_set == 'decode'){
                        if(isset($_SESSION[$value])) session_decode($value);
                    }
                }
            }
        }else{
            # 判断session传入参数是否有名称分割符号
            if(strpos($key, '.'))
                $key = array_filter(explode('.', $key));
            # 判断当前参数是否为数组，如果不是直接执行session操作
            if(is_array($key)){
                if(count($key) > 3){
                    # 异常提示：session无法支持超过3个维度的数组结构
                    try{
                        throw new Exception('Origin Method Error: Session can support more than three dimensional array structure');
                    }catch(Exception $e){
                        echo($e->getMessage());
                        exit();
                    }
                }else{
                    $_array_key = array_keys($key);
                    # 符合维度要求
                    if(count($key) == 3){
                        # 根据值状态进行session操作
                        if($value == null){
                            # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                            if(isset($_SESSION[$key[0]][$key[1]][$key[2]]))
                                $_receipt = $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]][$key[$_array_key[2]]];
                            else
                                $_receipt = $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]][$key[$_array_key[2]]] = null;
                        }else{
                            # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                            $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]][$key[$_array_key[2]]] = stripslashes($value);
                        }
                    }elseif(count($key) == 2){
                        # 根据值状态进行session操作
                        if($value == null){
                            # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                            if(isset($_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]]))
                                $_receipt = $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]];
                            else
                                $_receipt = $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]] = null;
                        }else{
                            # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                            $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]] = stripslashes($value);
                        }
                    }else{
                        # 根据值状态进行session操作
                        if($value == null){
                            # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                            if(isset($_SESSION[$key[$_array_key[0]]]))
                                $_receipt = $_SESSION[$key[$_array_key[0]]];
                            else
                                $_receipt = $_SESSION[$key[$_array_key[0]]] = null;
                        }else{
                            # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                            $_SESSION[$key[0]] = stripslashes($value);
                        }
                    }
                }
            }else{
                # 根据值状态进行session操作
                if($value == null){
                    # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                    if(isset($_SESSION[$key]))
                        $_receipt = $_SESSION[$key];
                    else
                        $_receipt = $_SESSION[$key] = null;
                }else{
                    # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                    $_SESSION[$key] = stripslashes($value);
                }
            }
        }
        # 判断php.ini是否设置了自动启用session会话,如果未开启，则在session编辑结束后关闭会话
        # session_write_close() 与 session_commit() 用法一致
        if(!ini_get('session.auto_start')) session_commit();
    }else{
        # 异常提示：会话名不符合命名规范
        try{
            throw new Exception('error: The session name does not conform to the naming conventions');
        }catch(Exception $e){
            echo($e->getMessage());
            exit();
        }
    }
    return $_receipt;
}