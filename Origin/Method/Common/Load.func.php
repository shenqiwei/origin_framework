<?php
/**
 * 文件检索及加载函数,处理预设结构类型
 * @access public
 * @param string $guide 文件路径，使用 :（冒号）作为连接符
 * !- 原结构参数param string $type 文件类型，用于区分不同作用文件，基础类型class（类），func（函数），cfg（配置）取消该结构
 * @param string $suffix 文件扩展名，文件扩展与文件类型名组成，完整的文件扩展名。例如：.class.php / .cfg.php
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
            $_suffix = '.class.php';
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
            if(!is_null($suffix)){
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
            Loading($_url, '.class.php');
        }
        elseif($_array[0] == 'Config'){
            $_url = str_replace(SLASH,':',RING).$guide;
            $_receipt = Loading($_url, '.cfg.php');
        }
        elseif($_array[0] == 'Interface'){
            array_shift($_array);
            $guide = implode(':', $_array);
            $_url = str_replace(SLASH,':',RING).'Kernel:'.$guide;
            Loading($_url,'.impl.php');
        }
        else{
            $_url = str_replace(SLASH,':',RING).'Kernel:'.$guide;
            Loading($_url,'.class.php');
        }
    }
    return $_receipt;
}
/**
 * call(呼叫)调用插件及公共组件方法，也可以用于调用新增公共控制器文件，或者函数包
 * @access public
 * @param $guide
 * @param $throws
 * @return mixed
 */
function Call($guide, $throws='enable')
{
    /**
     * 使用正则表达式对文件引导信息进行过滤
     * @var mixed $_receipt
     * @var string $_regular
     * @var string $_exception
     * @var mixed $_guide
     */
    $_receipt = null;
    # 创建引导信息验证正则表达式变量
    $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
    # 创建特例变量
    $_exception = null;
    # 验证引导信息是否符合要求
    if(is_true($_regular, $guide) === true){
        # 判断是否存在连接符号
        if(strpos($guide, ':')){
            # 拆分为数组结构
            $_guide = explode(':', $guide);
            # 创建默认扩展名
            $_suffix = Configurate('CLASS_SUFFIX');
            # 拼接参数信息，并判断是否存在于配置文件中
            if(Configurate('APPLICATION_'.strtoupper($_guide[0])) and Configurate(strtoupper($_guide[0]).'_SUFFIX')){
                $_suffix = Configurate(strtoupper($_guide[0]).'_SUFFIX');
                $_guide[0] = str_replace('/','',Configurate('APPLICATION_'.strtoupper($_guide[0])));
            }
            $guide= implode(':',$_guide);
            # 判断地址栏中路径信息是否不为空
            if($_SERVER['PATH_INFO']) $_map = explode('/', $_SERVER['PATH_INFO'])[1];
            # 判断函数处理变量是否被创建
            if(isset($_map))
                # 判断获取值与默认应用文件名是否相同
                if($_map != __APPLICATION__)
                    # 判断该值是否问应用目录
                    if(is_dir(ROOT.SLASH.Configurate('ROOT_APPLICATION').$_map))
                        $_master = $_map.'/';
            # 根据执行结构获取文件路径指向信息
            $_dir = isset($_master) ? $_master:__APPLICATION__;
            # 使用钩子公共方法引入文件
            $_receipt = Loading(str_replace('/',':',Configurate('ROOT_APPLICATION').$_dir.$guide), $_suffix, $throws);
        }
    }
    return $_receipt;
}