<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.3
 * @copyright 2015-2017
 */
/**
 * Cookie操作函数
 * @access public
 * @param string $key
 * @param string $value
 * @return mixed
 */
function Cookie($key, $value=null)
{
    /**
     * @var string $_receipt
     * @var string $_validate_key
    */
    $_receipt = null;
    # 创建session键名过滤正则
    $_validate_key = '/^[^\_\W]+([\.\_\-]?[^\_\W]+)?$/';
    # 获取cookie配置表
    $_cookie = Config('CONFIG');
    # 进行验证，如果键名结构验证失败，则抛出异常
    if(is_true($_validate_key, $key)){
        if(strpos($key, ':')){
            $_operate = explode(':', $key);
            if(count($_operate) > 3){
                # 异常提示：超出程序最大执行范围
                # 设定与session模块一致
                try{
                    throw new Exception('Is beyond the scope biggest execution');
                }catch(Exception $e){
                    $_output = new Origin\Kernel\Parameter\Output();
                    $_output->exception("Cookie Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
            # 判断session设置基本条件是否符合预设语法规则
            if(strtolower($_operate[0]) == 'cookie'){
                # 创建中间变量 resource
                $_resource = null;
                # 创建中间变量 set
                $_set = null;
                # 判断操作数组长度，
                if(count($_operate) == 3){
                    # 判断设置项是否存在，如果不存在抛出异常
                    if(!array_key_exists($_operate[1], $_cookie) and !array_key_exists($_operate[2], $_cookie)){
                        # 异常提示：框架暂不支持该session设置项
                        try{
                            throw new Exception('The framework of short duration does not support the session Settings');
                        }catch(Exception $e){
                            $_output = new Origin\Kernel\Parameter\Output();
                            $_output->exception("Cookie Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }else{
                        # 判断判断操作项参数位置，并赋入中间变量中
                        if(array_key_exists($_operate[2], $_cookie)){
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
                        if(array_key_exists(strtoupper($_set), $_cookie))
                            ini_set('session.'.strtolower($_set), $_cookie[strtoupper($_set)]);
                    }elseif($_resource == 'auto'){
                        if(array_key_exists(strtoupper($_set), $_cookie))
                            ini_set('session.'.strtolower($_set), $value);
                    }
                }else{
                    if(array_key_exists(strtoupper($_set), $_cookie))
                        $_receipt = ini_get('session.'.strtolower($_set));
                }
            }
        }else{
            setcookie($key, $value, $_cookie['COOKIE_LIFETIME'], $_cookie['COOKIE_PATH'], $_cookie['COOKIE_DOMAIN']);
        }
    }else{
        # 异常提示：会话名不符合命名规范
        try{
            throw new Exception('The cookie name does not conform to the naming conventions');
        }catch(Exception $e){
            $_output = new Origin\Kernel\Parameter\Output();
            $_output->exception("Cookie Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
    }
    return $_receipt;
}