<?php
/**
 * 文件检索及加载函数,处理预设结构类型
 * @access public
 * @param string $guide 文件路径，使用 :（冒号）作为连接符
 * @return null
 */
function Import($guide)
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
        # 将指引信息转为数组结构
        $_hook = explode('/',$guide);
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
                    try {
                        throw new Exception('The folder address ' . $_folder . ' does not exist');
                    } catch (Exception $e) {
                        $_output = new Origin\Kernel\Parameter\Output();
                        $_output->exception("Import Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }
        }
        # 判断完整文件路径是否存在，存在时，直接引入文件，反之抛出异常信息
        if(is_file($_folder.$_file.$_suffix)){
            $_receipt = include($_folder.$_file.$_suffix);
        }else{
            # 异常提示:文件加载失败
            try {
                throw new Exception('Origin Method Error[1002]: File ' . $_folder . $_file . $_suffix . ' loading failure');
            } catch (Exception $e) {
                $_output = new Origin\Kernel\Parameter\Output();
                $_output->exception("Import Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
    }else{
        # 异常提示：无法引入空地址文件
        try{
            throw new Exception('Origin Method Error[1005]: Unable to introduce empty address file');
        }catch(Exception $e){
            $_output = new Origin\Kernel\Parameter\Output();
            $_output->exception("Import Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
    }
    if(!is_array($_receipt)){
        $_receipt = null;
    }
    return $_receipt;
}