<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 * @context: Origin框架变量过滤封装类
 */
namespace Origin\Kernel\Graph;

use Origin\Kernel\File\File;
use Origin\Kernel\Parameter\Output;
use Exception;

/**
 * 模板调度类
 */
class View
{
    /**
     * 模板页加载方法
     * @access public
     * @param string $dir
     * @param string $page
     * @param array $param
     * @return null
    */
    static function view($dir,$page,$param)
    {
        # 转化文件路径
        $_guide = explode('/',$dir);
        # 判断结构模型
        $_dir = Config('DEFAULT_APPLICATION')."/";
        # 判断引导路径中是否存在多级文件
        if(count($_guide) > 1){
            for($i=0; $i<count($_guide);$i++){
                if(($i+1) == count($_guide))
                    $dir = $_guide[count($_guide)-1];
                else
                    $_dir = $_guide[$i].'/';
            }
        }
        # 获取应用目录
        $_url = str_replace('/', DS, "Application/{$_dir}");
        # 判断应用目录是否有效
        if(is_dir($_url)){
            # 获得前台模板目录
            $_url_view = str_replace('/', DS, $_url.Config('APPLICATION_VIEW')."/");
            # 判断前台模板目录是否有效
            if(is_dir($_url_view)){
                # 判断应用控制器对应前台模板目录是否有效
                if(is_dir($_url_view.$dir)){
                    # 调用模板
                    $_page = $_url_view.$dir.DS.$page.'.html';
                    if(is_file($_page)){
                        # 加载参数内容
                        foreach($param as $_key => $_value){
                            $$_key = $_value;
                        }
                        # 清除寄存数组信息
                        unset($param);
                        # 执行模板解析
                        $_label = new Label($_page);
                        # 获取解析后文件内容
                        $_cache_code = $_label->execute();
                        if(DEBUG){
                            $_debug_tmp = "Resource/Buffer/Debug/".sha1($_page).".tmp";
                            $_file = new File();
                            $_cache_uri = str_replace("/",DS,ROOT.$_debug_tmp);
                            if(!is_file($_cache_uri) or time() > strtotime("+30 minutes",filemtime($_cache_uri))){
                                $_file->write($_debug_tmp,"cw",$_cache_code);
                            }
                        }else{
                            # 获取解析后代码,生成临时缓存文件
                            $_cache_file = tmpfile();
                            # 写入解析后模板内容
                            fwrite($_cache_file,$_cache_code);
                            # 通过数据流获取缓存文件临时路径信息
                            $_cache_uri = stream_get_meta_data($_cache_file)["uri"];
                        }
                        # 调用缓存文件
                        include($_cache_uri);
                        # 关闭缓存文件，系统自动释放缓存空间
                        if(isset($_cache_file))
                            fclose($_cache_file);
                    }else{
                        # 异常提示：该对象模板不存在
                        try{
                            throw new Exception('The object template '.$_page.' does not exist');
                        }catch(Exception $e){
                            $_output = new Output();
                            $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }
                }else{
                    # 异常提示：该对象模板不存在
                    try{
                        throw new Exception('The object template dir '.$_url_view.$dir.' does not exist');
                    }catch(Exception $e){
                        $_output = new Output();
                        $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }else{
                # 异常提示：请在当前路径下创建view文件夹
                try{
                    throw new Exception('Please create the (view) folder under the current path:'.$_url);
                }catch(Exception $e){
                    $_output = new Output();
                    $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }else{
            try{
                throw new Exception('The folder address '.$_url.' does not exist');
            }catch(Exception $e){
                $_output = new Output();
                $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return null;
    }
}