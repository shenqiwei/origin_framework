<?php
/**
 * 公共配置信息引导函数
 * @access public
 * @param string $guide 配置名称，不区分大小写
 * @return string
 */
function Configurate($guide)
{
    /**
     * @var array $_array
     * @var string $_receipt
     * @var mixed $_config
     */
    # 创建结果集变量
    $_receipt = null;
    # 创建配置结构变量
    $_config = null;
    # 创建配置寄存变量
    $_array = Import('Config:Config');
    # 判断引导参数是否有效
    if(Rule($guide)){
        # 判断参数中是否存在引导连接符，当存在引导连接符，则将参数转为数组并赋入配置变量中，反之则直接赋入配置变量中
        if(strpos($guide,':')){
            $_config = explode(':',$guide);
        }else{
            $_config = strval(trim(strtoupper($guide)));
        }
        # 配置变量是否为数组，跟返回状态执行不同的操作
        if(is_array($_config)){
            # 遍历引导信息
            for($i=0;$i<count($_config);$i++){
                # 判断数组元素信息是否为数组中的键名，如果是将对应元素值信息存入数组变量中，
                if(array_key_exists(strtoupper($_config[$i]), $_array)){
                    $_array = $_array[strtoupper($_config[$i])];
                    # 判断元素值是否为数组，如果是继续进行查找和验证，反之赋入返回变量中
                    if(is_array($_array)){
                        continue;
                    }else{
                        $_receipt = $_array;
                        break;
                    }
                }
            }
        }else{
            # 判断当前配置名称是否存在于配置中，如果存在赋入返回变量中
            if(array_key_exists(strtoupper($_config), $_array)){
                $_receipt = $_array[strtoupper($_config)];
            }
        }
    }
    return $_receipt;
}

/**
 * Config公共配置信息调用方法,优先调用用户配置文件，在用户配置文件不存在或者无配置项时，调用系统配置文件
 * @access public
 * @param string $guide
 * @return null
 */
function Config($guide)
{
    /**
     * 执行结构为两种，一种是直接调用公共配置文件或者主配置文件，另一种是筛查配置信息位置，在读取配置信息
     * 筛查配置信息，会先检索公共配置信息，然后是主配置信息，最后检索自定义配置文件.
     * 公共配置文件及主配置文件始于框架本体共同存在，所以只在最后检索自定义配置文件是，才会报错异常错误.
     * @var string $_receipt
     * @var array $_config
     * @var string $_regular
     * @var array $_guide
     * @var int $i
     */
    # 创建返回值变量
    $_receipt = null;
    # 创建配置信息初始变量
    $_config = null;
    # 创建引导信息验证正则表达式变量
    $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
    # 验证指引结构信息
    if(is_true($_regular, $guide) === true){
        # 判断是否存在预设连接符号
        if(strpos($guide, ':')){
            # 拆分数组
            $_guide = explode(':', $guide);
            # 引入自定义配置文件
            $_config = Call('Config:'.$_guide[0], 'disabled');
            # 判断有无返回信息数组
            if(!$_config){
                for($i=1;$i<count($_guide);$i++){
                    if(is_array($_guide[$i])){
                        $_guide = $_guide[$i];
                    }else{
                        $_receipt = $_guide[$i];
                        break;
                    }
                }
            }
            if(is_null($_receipt)){
                # 使用钩子函数调用公共配置文件
                $_config = Call('Config:Config', 'disabled');
                # 判断返回信息
                if($_config){
                    # 引导信息数组，并对数据内容进行匹配
                    for($i=0;$i<count($_guide);$i++){
                        if(is_array($_config[$_guide[$i]])){
                            $_guide = $_config[$_guide[$i]];
                        }else{
                            $_receipt = $_config[$_guide[$i]];
                            break;
                        }
                    }
                }
            }
            if(is_null($_receipt)){
                # 调取公共配置信息
                $_config = Common('Config:Config');
                # 判断返回信息
                if($_config){
                    # 引导信息数组，并对数据内容进行匹配
                    for($i=0;$i<count($_guide);$i++){
                        if(is_array($_config[$_guide[$i]])){
                            $_guide = $_config[$_guide[$i]];
                        }else{
                            $_receipt = $_config[$_guide[$i]];
                            break;
                        }
                    }
                }
            }
            if(is_null($_receipt)){
                # 引导信息数组，并对数据内容进行匹配
                for($i=0;$i<count($_guide);$i++){
                    if(is_array($_config[$_guide[$i]])){
                        $_guide = $_config[$_guide[$i]];
                    }else{
                        $_receipt = $_config[$_guide[$i]];
                        break;
                    }
                }
            }
        }else{
            # 调用默认配置文件信息
            $_config = Call('Config:Config', 'disabled');
            if($_config[$guide]){
                $_receipt = $_config[$guide];
            }else{
                # 调取公共配置信息
                $_config = Common('Config:Config');
                if($_config[$guide]){
                    $_receipt = $_config[$guide];
                }else{
                    # 调取主配置信息
                    $_receipt = Configurate($guide);
                }
            }
        }
    }
    return $_receipt;
}