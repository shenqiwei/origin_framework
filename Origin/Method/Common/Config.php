<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Function.Method.Config *
 * version: 0.1*
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 */
/**
 * 公共配置信息引导函数
 * @access public
 * @param string $guide 配置名称，不区分大小写
 * @return string
 */
function Configuration($guide)
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