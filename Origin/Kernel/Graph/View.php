<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2017
 * @context: IoC 变量过滤封装类
 */
namespace Origin\Kernel\Graph;

use Origin\Kernel\Parameter\Output;

/**
 * 模板调度类
 */
class View
{
    /**
     * 模板文件夹信息
     * @var string $_Dir
    */
    private $_Dir = null;
    /**
     * 模板页名称信息
     * @var string $_Page
    */
    private $_Page = null;
    /**
     * 构造方法用于获取模板对象
     * @access public
     * @param string $dir
     * @param string $page
     */
    function __construct($dir, $page)
    {
        $this->_Dir = $dir;
        $this->_Page = $page;
    }
    /**
     * 模板页加载方法
     * @access public
     * @param array $param
     * @return null
    */
    function view($param)
    {
        # 转化文件路径
        $_guide = explode('/',$this->_Dir);
        # 判断结构模型
        $_dir = Config('DEFAULT_APPLICATION')."/";
        # 判断引导路径中是否存在多级文件
        if(count($_guide) > 1){
            for($i=0; $i<count($_guide);$i++){
                if(($i+1) == count($_guide))
                    $this->_Dir = $_guide[count($_guide)-1];
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
                if(is_dir($_url_view.$this->_Dir)){
                    # 调用模板
                    $_page = $_url_view.$this->_Dir.DS.$this->_Page.'.html';
                    if(is_file($_page)){
                        $_label = new Label($_page, $param);
                        echo($_label->execute());
                    }else{
                        # 异常提示：该对象模板不存在
                        try{
                            throw new \Exception('The object template '.$_page.' does not exist');
                        }catch(\Exception $e){
                            errorLogs($e->getMessage());
                            $_output = new Output();
                            $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }
                }else{
                    # 异常提示：该对象模板不存在
                    try{
                        throw new \Exception('The object template dir '.$_url_view.$this->_Dir.' does not exist');
                    }catch(\Exception $e){
                        errorLogs($e->getMessage());
                        $_output = new Output();
                        $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }else{
                # 异常提示：请在当前路径下创建view文件夹
                try{
                    throw new \Exception('Please create the (view) folder under the current path:'.$_url);
                }catch(\Exception $e){
                    errorLogs($e->getMessage());
                    $_output = new Output();
                    $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }else{
            # 异常提示：主文件夹地址不存在
            try{
                throw new \Exception('The folder address '.$_url.' does not exist');
            }catch(\Exception $e){
                errorLogs($e->getMessage());
                $_output = new Output();
                $_output->exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return null;
    }
}