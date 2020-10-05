<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2017
 */
/**
 * Config公共配置信息调用方法,优先调用用户配置文件，在用户配置文件不存在或者无配置项时，调用系统配置文件
 * @access public
 * @param string $item 配置项
 * @return null
 */
function config($item)
{
    # 创建返回值变量
    $_receipt = null;
    # 创建配置信息初始变量
    $_configuration = null;
    # 引入应用配置文件
    $_config_file = str_replace(RE_DS,DS,ROOT."application/config/config.php");
    if(is_file($_config_file))
        $_configuration = include($_config_file);
    else
        goto def;
    if(!is_null($_configuration))
        $_receipt = configuration($item,$_configuration);
    if(!is_null($_receipt))
        goto re;
    def:
    $_configuration = include(str_replace(RE_DS,DS,ROOT."origin/config/config.php"));
    $_receipt = configuration($item,$_configuration);
    re:
    return $_receipt;
}
/**
 * 公共配置信息引导函数
 * @access public
 * @param string $item 配置项，不区分大小写
 * @param array $configuration 配置列表
 * @return string
 */
function configuration($item,$configuration)
{
    # 创建结果集变量
    $_receipt = null;
    # 创建配置结构变量
    $_config = null;
    # 创建配置寄存变量
    $_array = $configuration;
    # 判断引导参数是否有效
    if(preg_match('/^[^_\W\s]+((\\\:|_)?[^_\W\s]+)*$/u', $item)){
        # 判断参数中是否存在引导连接符，当存在引导连接符，则将参数转为数组并赋入配置变量中，反之则直接赋入配置变量中
        if(strpos($item,':')){
            $_config = explode(':',$item);
        }else{
            $_config = strval(trim(strtoupper($item)));
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