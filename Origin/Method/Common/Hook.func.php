<?php
/**
 * 文件检索及加载函数,处理预设结构类型
 * @access public
 * @param string $guide 文件路径，使用 :（冒号）作为连接符
 * @param string $type 文件类型，用于区分不同作用文件，基础类型class（类），func（函数），cfg（配置）
 * @param string $suffix 文件扩展名，文件扩展与文件类型名组成，完整的文件扩展名。例如：.class.php / .cfg.php
 * @param string $throws 是否抛出异常信息
 * @return null
 */
function Hook($guide, $type=null, $suffix=null, $throws='enable')
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
            # 创建文件域类型作用范围
            $_type = '/^(class|func|function|impl|implements|interface|controller|method|common|cfg|config|action|data|file|
            graph|math|message|info|param|bean|beans|map|mapping|filter|model||auto)$/';
            # 双结构解释数组
            $_array = array(
                'controller' => 'class', 'function' => 'func', 'method' => 'func', 'common' => 'func',
                'config' => 'cfg', 'action' => 'act', 'message' => 'info', 'param' => 'bean', 'beans' => 'bean',
                'map' => 'mapping', 'implements' => 'impl', 'interface' => 'impl',
            );
            # 自定义文件域名公式
            $_regular = '/^[\.][^\_\W]+([\.][^\_\W]+)*$/';
            # 非法结构
            $_danger = '/([\W\_])+/';
            # 替换结构
            $_replace = '.';
            # 创建文件名空变量
            $_file = null;
            # 限定文件扩展名
            $_suffix = '.php';
            # 循环指引路径数组
            for($i=0;$i<count($_hook);$i++){
                # 判断是否是最后一个组数元素，当遍历到最后一个元素时，跳过验证结构
                if($i == count($_hook)-1){
                    $_file = SLASH.$_hook[$i];
                    continue;
                }else{
                    # 组装路径信息，随遍历深度进行路径拼接
                    if($i==0){
                        $_folder = $_folder.$_hook[$i];
                    }else{
                        $_folder = $_folder.SLASH.$_hook[$i];
                    }
                    # 判断每次遍历组装后的文件夹路径是否存在，当该路径存在是跳过，反之抛出异常信息
                    if(is_dir($_folder)){
                        continue;
                    }else{
                        # 异常提示：文件夹地址不存在
                        if($throws != 'disabled') {
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
            # 判断文件类型是否符合要求
            if(preg_match($_type, $type)){
                # 判断例外规则
                if($type == 'auto'){
                    # 调用用户自定义文件类型，当文件不为空时，使用用户自定义类型拼接扩展名，反之使用默认扩展名
                    if($suffix != null){
                        if(preg_match($_regular, $suffix)){
                            $_suffix = $suffix;
                        }else{
                            $_suffix = preg_replace($_danger, $_replace, $suffix);
                        }
                    }
                }else{
                    # 当文件类型符合作用域要求时，使用文件类型拼接扩展名
                    if($type !== null){
                        if(array_key_exists($type, $_array)){
                            $type = $_array[$type];
                        }
                        $_suffix = '.'.$type.$_suffix;
                    }
                }
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
 * 内核控制器加载函数
 * @access public
 * @param string $guide 应用调用地址使用 ：（冒号）作为连接符，
 * 内核控制器地址：Controller:* / 不写，
 * 应用控制器：Application:*，
 * 函数地址：Function:*
 * @return null
 */
function Import($guide)
{
    /**
     * @var array $_array
     * @var string $_url
     */
    $_receipt = null;
    if(strpos($guide,':')){
        $_array = explode(':', $guide);
        if($_array[0] == 'Application'){
            $_url = str_replace(SLASH,':',RING).$guide;
            Hook($_url, 'controller');
        }
        elseif($_array[0] == 'Config'){
            $_url = str_replace(SLASH,':',RING).$guide;
            $_receipt = Hook($_url, null, null, 'config');
        }
        elseif($_array[0] == 'Interface'){
            array_shift($_array);
            $guide = implode(':', $_array);
            $_url = str_replace(SLASH,':',RING).'Kernel:'.$guide;
            Hook($_url,'interface');
        }
        else{
            $_url = str_replace(SLASH,':',RING).'Kernel:'.$guide;
            Hook($_url,'controller');
        }
    }
    return $_receipt;
}