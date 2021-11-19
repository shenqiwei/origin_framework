<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2017
 */
/**
 * Config公共配置信息调用方法,优先调用用户配置文件，在用户配置文件不存在或者无配置项时，调用系统配置文件
 * @access public
 * @param string $item 配置项
 * @return mixed 返回配置信息或配置列表
 */
function config(string $item)
{
    # 创建返回值变量
    $receipt = null;
    $file = replace(ROOT."/common/config/config.php");
    # 引入主配置文件
    $configuration = include("$file");
    # 判断引导参数是否有效
    if(preg_match('/^[^_\W\s]+((\\\:|_)?[^_\W\s]+)*$/u', $item)){
        # 判断参数中是否存在引导连接符，当存在引导连接符，则将参数转为数组并赋入配置变量中，反之则直接赋入配置变量中
        if(strpos($item,':'))
            $config = explode(':',$item);
        else
            $config = trim(strtoupper($item));
        # 配置变量是否为数组，跟返回状态执行不同的操作
        if(is_array($config)){
            # 遍历引导信息
            for($i=0;$i<count($config);$i++){
                # 判断数组元素信息是否为数组中的键名，如果是将对应元素值信息存入数组变量中，
                if(array_key_exists(strtoupper($config[$i]), $configuration)){
                    $array = $configuration[strtoupper($config[$i])];
                    # 判断元素值是否为数组，如果是继续进行查找和验证，反之赋入返回变量中
                    if(is_array($array))
                        continue;
                    else{
                        $receipt = $array;
                        break;
                    }
                }
            }
        }else{
            # 判断当前配置名称是否存在于配置中，如果存在赋入返回变量中
            if(array_key_exists(strtoupper($config), $configuration))
                $receipt = $configuration[strtoupper($config)];
        }
    }
    return $receipt;
}