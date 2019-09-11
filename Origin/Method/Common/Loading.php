<?php
/**
 * 文件检索及加载函数,处理预设结构类型
 * @access public
 * @param string $guide 文件路径，使用 :（冒号）作为连接符
 * !- 原结构参数param string $type 文件类型，用于区分不同作用文件，基础类型class（类），func（函数），cfg（配置）取消该结构
 * @param string $suffix 文件扩展名，文件扩展与文件类型名组成，完整的文件扩展名。例如：.php
 * @param string $throws 是否抛出异常信息
 * @return null
 */
function Loading($guide, $suffix=null, $throws='enable')
{
    /**
     * @var mixed $_hook 指引结构数组
     * @var mixed $_type 预设文件类型正则表达式
     * @var mixed $_array 文件类型描述结构数组
     * @var string $_regular 文件扩展名结构正则表达式
     * @var string $_folder 文件夹物理路径
     * @var string $_danger
     * @var string $_file 文件名变量
     * @var string $_suffix 扩展名
     * @var int $i
     */
    $_receipt = null;
    # 判断指引信息是否为空
    if($guide){
        # 判断连接符号是否存在
        if(Rule($guide) and strpos($guide,':')){
            # 将指引信息转为数组结构
            $_hook = explode(':',$guide);
            # 创建根路径信息
            $_folder = ROOT;
            # 创建文件名空变量
            $_file = null;
            # 限定文件扩展名
            $_suffix = '.php';
            # 循环指引路径数组
            for($i=0;$i<count($_hook);$i++){
                # 判断是否是最后一个组数元素，当遍历到最后一个元素时，跳过验证结构
                if($i == count($_hook)-1){
                    $_file = DS.$_hook[$i];
                    continue;
                }else{
                    # 组装路径信息，随遍历深度进行路径拼接
                    if($i==0){
                        $_folder = $_folder.$_hook[$i];
                    }else{
                        $_folder = $_folder.DS.$_hook[$i];
                    }
                    # 判断每次遍历组装后的文件夹路径是否存在，当该路径存在是跳过，反之抛出异常信息
                    if(is_dir($_folder)){
                        continue;
                    }else{
                        # 异常提示：文件夹地址不存在
                        if(strtolower($throws) != 'disabled') {
                            try {
                                throw new Exception('Origin Method Error[1001]: The folder address ' . $_folder . ' does not exist');
                            } catch (Exception $e) {
                                echo($e->getMessage());
                                exit();
                            }
                        }
                        break;
                    }
                }
            }
            # 调用用户自定义文件类型，当文件不为空时，使用用户自定义类型拼接扩展名，反之使用默认扩展名
            if($suffix != null){
                $_suffix = $suffix;
            }
            # 判断完整文件路径是否存在，存在时，直接引入文件，反之抛出异常信息
            if(is_file($_folder.$_file.$_suffix)){
                $_receipt = include($_folder.$_file.$_suffix);
            }else{
                # 异常提示:文件加载失败
                if($throws != 'disabled') {
                    try {
                        throw new Exception('Origin Method Error[1002]: File ' . $_folder . $_file . $_suffix . ' loading failure');
                    } catch (Exception $e) {
                        echo($e->getMessage());
                        exit();
                    }
                }
            }
        }else{
            # 异常提示：文件引导地址无效
            if($throws != 'disabled') {
                try {
                    throw new Exception('Origin Method Error[1003]: Direct address ' . $guide . ' is invalid');
                } catch (Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
        }
    }else{
        # 异常提示：无法引入空地址文件
        if($throws != 'disabled'){
            try{
                throw new Exception('Origin Method Error[1005]: Unable to introduce empty address file');
            }catch(Exception $e){
                echo($e->getMessage());
                exit();
            }
        }
    }
    if(!is_array($_receipt)){
        $_receipt = null;
    }
    return $_receipt;
}
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
    $_files = str_replace('/', DS, ROOT."Apply/".Configuration('ROUTE_CATALOGUE'));
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
                        throw new \Exception('Origin Class Error: Routing structure configuration errors');
                    }catch(\Exception $e){
                        echo($e->getMessage());
                        exit(0);
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
                                throw new \Exception('Origin Class Error: Not set the parameter name');
                            }catch(\Exception $e){
                                echo($e->getMessage());
                                exit(0);
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