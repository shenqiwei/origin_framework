<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架变量过滤封装类
 */
namespace Origin\Package;

use Exception;

class View
{
    /**
     * 模板页加载方法
     * @access public
     * @param string $dir 地址目录
     * @param string $page 模板信息
     * @param array $param 参数值内容
     * @param float $time 起始加载时间
     * @return void
     * @context
    */
    static function view($dir,$page,$param,$time)
    {
        # 转化文件路径
        $guide = explode('/',$dir);
        # 判断结构模型
        $root = DEFAULT_APPLICATION."/";
        # 判断引导路径中是否存在多级文件
        if(count($guide) > 1){
            for($i=0; $i<count($guide);$i++){
                if(($i+1) == count($guide))
                    $dir = $guide[count($guide)-1];
                else
                    $root = $guide[$i].'/';
            }
        }
        # 获取应用目录
        $url = replace("application/{$root}");
        # 判断应用目录是否有效
        if(is_dir($url)){
            # 获得前台模板目录
            $url_view = replace($url."template/");
            # 判断前台模板目录是否有效
            if(is_dir($url_view)){
                # 判断应用控制器对应前台模板目录是否有效
                if(is_dir($url_view.$dir)){
                    # 调用模板
                    $page = $url_view.$dir.DS.$page.'.html';
                    if(is_file($page)){
                        # 创建运行时间模板
                        $temp = null;
                        if(DEBUG and TIME){
                            $temp = ORIGIN."template/200.html";
                            if(is_file(replace($temp))){
                                $load_end = explode(" ",microtime());
                                $load_end = floatval($load_end[0])+floatval($load_end[1]);
                                $time= round(($load_end-$time)*1000,2);
                                $temp = file_get_contents($temp);
                                $temp = str_replace('{/time/}',$time,$temp);
                            }
                        }
                        # 加载参数内容
                        foreach($param as $key => $value){
                            $$key = $value;
                        }
                        # 清除寄存数组信息
                        unset($param);
                        # 执行模板解析
                        $label = new Label($page);
                        # 获取解析后文件内容
                        $cache_code = $label->execute();
                        if(config("ROOT_USE_BUFFER")){
                            $debug_tmp = "resource/public/cache/".sha1($page).".tmp";
                            $file = new File();
                            $cache_uri = replace(ROOT.DS.$debug_tmp);
                            if(!is_file($cache_uri) or time() > strtotime("+30 minutes",filemtime($cache_uri))){
                                $file->write($debug_tmp,"cw",$cache_code.$temp);
                            }
                        }else{
                            # 获取解析后代码,生成临时缓存文件
                            $cache_file = tmpfile();
                            # 写入解析后模板内容
                            fwrite($cache_file,$cache_code.$temp);
                            # 通过数据流获取缓存文件临时路径信息
                            $cache_uri = stream_get_meta_data($cache_file)["uri"];
                        }
                        # 调用缓存文件
                        include("{$cache_uri}");
                        # 关闭缓存文件，系统自动释放缓存空间
                        if(isset($cache_file))
                            fclose($cache_file);
                    }else{
                        # 异常提示：该对象模板不存在
                        try{
                            throw new Exception('The object template '.$page.' does not exist');
                        }catch(Exception $e){
                            exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }
                }else{
                    # 异常提示：该对象模板不存在
                    try{
                        throw new Exception('The object template dir '.$url_view.$dir.' does not exist');
                    }catch(Exception $e){
                        exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }else{
                # 异常提示：请在当前路径下创建view文件夹
                try{
                    throw new Exception('Please create the (view) folder under the current path:'.$url);
                }catch(Exception $e){
                    exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }else{
            try{
                throw new Exception('The folder address '.$url.' does not exist');
            }catch(Exception $e){
                exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
    }
}