<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架文件夹操作封装
 */
namespace Origin\Package;

use Exception;

class Folder
{
    /**
     * @access protected
     * @var string $Root 根目录
    */
    protected $Root = ROOT;

    /**
     * 构造方法，设置根目录地址
     * @access public
     * @param string|null $root 根目录地址
     * @return void
    */
    function __construct(?string $root=null)
    {
        if(!is_null($root))
            $this->Root = $root;
    }

    /**
     * @access protected
     * @var string $Breakpoint 断点变量
     */
    protected $Breakpoint = null;

    /**
     * 断点信息返回
     * @access public
     * @return string 返回断点信息
     */
    function getBreakpoint(): ?string
    {
        return $this->Breakpoint;
    }

    /**
     * @access protected
     * @var string $Error 错误信息
     */
    protected $Error = null;

    /**
     * 获取错误信息
     * @access public
     * @return string|null 返回异常信息
     */
    function getError(): ?string
    {
        return $this->Error;
    }

    /**
     * 创建文件夹
     * @access public
     * @param string $folder 文件夹地址
     * @param boolean $autocomplete 自动补全完整路径，默认值 false
     * @param boolean $throw 捕捉异常
     * @return boolean 返回执行结果状态值
    */
    function create(string $folder, bool $autocomplete=false, bool $throw=false): bool
    {
        # 设置返回对象
        $receipt = false;
        # 判断文件夹是否已创建完成
        if(file_exists($folder = replace(ROOT.DS.$folder)))
            $receipt = true;
        else{
            if(!mkdir($folder)){
                $folder = explode('/',$folder);
                $guide = null;
                for($i = 0;$i < count($folder);$i++){
                    if(empty($i))
                        $guide = $folder[$i];
                    else
                        $guide .= DS.$folder[$i];
                    if(is_dir(ROOT.DS.$guide))
                        continue;
                    else{
                        if($autocomplete){
                            if(!mkdir(ROOT.DS.$guide))
                                $this->Breakpoint = $folder[$i];
                        }else
                            $this->Breakpoint = $folder[$i];
                    }
                }
            }else
                $receipt = true;
            if(!$receipt){
                # 错误代码：00101，错误信息：文件创建失败
                $this->Error = "Create folder [$folder] failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("Folder Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }
        }
        return $receipt;
    }

    /**
     * 删除文件夹
     * @access public
     * @param string $folder 文件夹地址
     * @param boolean $throw 捕捉异常
     * @return boolean 返回执行结果状态值
    */
    function remove(string $folder, bool $throw=false): bool
    {
        # 设置返回对象
        $receipt = false;
        if(!file_exists($folder = replace(ROOT.DS.$folder)))
            $receipt = true;
        else{
            if(!rmdir($folder)) {
                $this->Error = "Remove folder [$folder] failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("Folder Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }else
                $receipt = true;
        }
        return $receipt;
    }

    /**
     * 文件夹重命名
     * @access public
     * @param string $folder 文件地址
     * @param string $name 新名称
     * @param boolean $throw 捕捉异常
     * @return boolean 返回执行结果状态值
    */
    function rename(string $folder, string $name, bool $throw=false): bool
    {
        # 设置返回对象
        $receipt = false;
        if(file_exists($folder = replace(ROOT.DS.$folder))){
            if (!rename($folder, $name)) {
                # 错误代码：00102，错误信息：文件夹重命名失败
                $this->Error = "Folder [$folder] rename failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("Folder Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }
        }else{
            $this->Error = "The folder is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("Folder Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $receipt;
    }

    /**
     * 获取文件夹信息
     * @access public
     * @param string $folder 文件夹地址
     * @return array 返回执行结果状态值
    */
    function get(string $folder): array
    {
        $receipt = [];
        if(file_exists($directory = replace(ROOT.DS.$folder))){
            if($dir = opendir($directory)){
                # 执行列表遍历
                while($folder = readdir($dir) !== false){
                    $info = array(
                        "folder_name" => $folder,
                        "folder_size" => filesize($folder),
                        "folder_type" => filetype($folder),
                        "folder_change_time" => filectime($folder),
                        "folder_access_time" => fileatime($folder),
                        "folder_move_time" => filemtime($folder),
                        "folder_owner" => fileowner($folder),
                        "folder_limit" => fileperms($folder),
                        "folder_read" => is_readable($folder),
                        "folder_write" => is_writable($folder),
                        "folder_execute" => is_executable($folder),
                        "folder_create_type" => is_uploaded_file($folder)?"online":"location",
                        "folder_uri" => $directory.DS.$folder,
                    );
                    array_push($receipt,$info);
                }
                # 释放
                closedir($dir);
            }
        }
        return $receipt;
    }
}