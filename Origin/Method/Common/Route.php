<?php

/**
 * 路由执行函数
 * @access public
 * @param string $route 访问地址路径（路由路径）
 * @return null
 */
function Route($route){
    # 转化路径为数组结构
    $_path = explode('/',trim($route,'/'));
    # 创建对象变量
    $_obj = null;
    # 创建指针变量，起始位置0
    $_i = 0;
    # 循环处理路径参数
    while(true){
        # 判断结构中存在扩展名连接符号
        if(strpos($_path[$_i],'.')){
            # 消除扩展名信息
            $_path[$_i] = substr($_path[$_i], 0, strpos($_path[$_i],'.'));
        }
        # 指针下移
        $_i += 1;
        # 设置跳出结构条件
        if($_i >= count($_path)) break;
    }
    # 创建返回变量
    $_receipt = implode('/', $_path);
    # 创建路由文件目录变量
    $_files = str_replace('/', DS, ROOT."Application/".Configuration('ROUTE_CATALOGUE'));
    # 判断文件是否存在
    if(is_dir($_files)){
        # 判断路由文件是否存在
        if(is_file($_files.Configuration('ROUTE_FILES'))){
            # 获取路由配置信息
            $_obj = require_once($_files.Configuration('ROUTE_FILES'));
        }
    }
    # 判断路径信息是否有效
    if(count($_path) > 0){
        # 判断路由信息是否有效
        if (is_array($_obj) and count($_obj) > 0){
            # 遍历路由信息，用于比对路由信息
            for ($i = 0; $i < count($_obj); $i++){
                # 判断路由文件是否合法
                if(!is_array($_obj[$i])){
                    # 异常信息：路由结构配置错误
                    try {
                        throw new Exception('Routing structure configuration errors');
                    }catch(Exception $e){
                        $_output = new Origin\Kernel\Parameter\Output();
                        $_output->exception("Route Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
                # 匹配路由信息
                if(strpos($_receipt, $_obj[$i]['route']) === false or strpos($_receipt, $_obj[$i]['route']) > 0) continue;
                # 截取剩余路由信息(参数信息)
                $_param = trim(substr($_receipt, strlen($_obj[$i]['route'])),'/');
                # 获取映射信息
                $_receipt = $_obj[$i]['mapping'];
                # 判断是否存在参数段路由
                if(!empty($_param) and array_key_exists('param', $_obj[$i])){
                    # 遍历路由参数数组
                    for($j = 0; $j < count($_obj[$i]['param']); $j++){
                        # 转化参数路由
                        $_param = explode('/', $_param);
                        # 检查元素结构
                        if(!array_key_exists('name', $_obj[$i]['param'][$j])){
                            # 异常信息：未设置参数名称
                            try {
                                throw new Exception('Not set the parameter name');
                            }catch(Exception $e){
                                $_output = new Origin\Kernel\Parameter\Output();
                                $_output->exception("Route Error",$e->getMessage(),debug_backtrace(0,1));
                                exit();
                            }
                        }
                        # 获取默认值
                        $_default = array_key_exists('default', $_obj[$i]['param'][$j]) ? $_obj[$i]['param'][$j]['default'] : null;
                        # 判断数据类型
                        $_type = array_key_exists('type',$_obj[$i]['param'][$j]) ? $_obj[$i]['param'][$j]['type'] : 'string';
                        switch($_type){
                            case 'int':
                            case 'integer':
                                echo($_param[$j]);
                                # 创建get参数对象信息
                                $_GET[$_obj[$i]['param'][$j]['name']] = ((!empty($_param[$j]) and intval($_param[$j]) != 0 )or $_param[$j] != null) ? intval($_param[$j]) : (is_null($_default)) ? null : intval($_default);
                                break;
                            case 'float':
                            case 'double':
                                # 创建get参数对象信息
                                $_GET[$_obj[$i]['param'][$j]['name']] = (!empty($_param[$j]) or $_param[$j] != null) ? intval($_param[$j]) : (is_null($_default)) ? null : doubleval($_default);
                                break;
                            case 'boolean':
                                # 创建get参数对象信息
                                $_GET[$_obj][$i]['param'][$j]['name'] = ((!empty($_param[$j]) and intval($_param[$j]) != 0 and boolval($_param[$j]) != false )or $_param[$j] != null) ? boolval($_param[$j]) : (is_null($_default)) ? null : boolval($_default);
                                break;
                            case 'string':
                            default:
                                # 创建get参数对象信息
                                $_GET[$_obj[$i]['param'][$j]['name']] = (!empty($_param[$j]) or $_param[$j] != null) ? strval($_param[$j]) : (is_null($_default)) ? null : strval($_default);
                                break;
                        }
                    }
                }
                # 跳出循环结束验证
                break;
            }
        }
    }
    return $_receipt;
}