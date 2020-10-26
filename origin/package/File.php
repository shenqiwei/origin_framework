<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架文件操作封装
 */
namespace Origin\Package;

use Exception;

class File extends Folder
{
    /**
     * @access public
     * @param string $file 文件地址
     * @param boolean $autocomplete 自动补全完整路径，默认值 false
     * @param boolean $throw 捕捉异常
     * @return boolean
     * @context 创建文件夹
     */
    function create($file, $autocomplete=false,$throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        # 判断文件夹是否已创建完成
        if(file_exists($_folder = replace(ROOT.DS.$file)))
            $_receipt = true;
        else{
            if($_file = fopen($_folder, 'w')){
                $_receipt = true;
                fclose($_file);
            }else{
                # 获取文件夹信息
                $_dir = substr($file,0,strrpos($file,"/"));
                # 调用父类create方法
                if(parent::create($_dir,$autocomplete,true)){
                    if($_file = fopen($_folder, 'w')){
                        $_receipt = true;
                        fclose($_file);
                    }
                }else{
                    $this->Breakpoint = parent::getBreakpoint();
                }
            }
            if(!$_receipt){
                # 错误代码：00101，错误信息：文件创建失败
                $this->Error = "Create file [{$file}] failed";
                try {
                    throw new Exception($this->Error);
                } catch (Exception $e) {
                    exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
                    exit();
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $file 文件夹地址
     * @param boolean $throw 捕捉异常
     * @return boolean
     * @context 删除文件夹
     */
    function remove($file,$throw=false){
        # 设置返回对象
        $_receipt = false;
        if(!file_exists($_folder = replace(ROOT.DS.$file)))
            $_receipt = true;
        else{
            if(!unlink($_folder)) {
                $this->Error = "Remove file [{$file}] failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }else
                $_receipt = true;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $folder 文件地址
     * @param string $name 新名称
     * @param boolean $throw 捕捉异常
     * @return boolean
     * @context 文件重命名
     */
    function rename($folder, $name, $throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        if(file_exists($_folder = replace(ROOT.DS.$folder))){
            if (!rename($_folder, $name)) {
                # 错误代码：00102，错误信息：文件重命名失败
                $this->Error = "File [{$_folder}] rename failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }
        }else{
            $this->Error = "The file is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("File Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $file 文件路径
     * @param string $operate 操作类型
     * @param boolean $throw 捕捉异常
     * @return mixed
     * @contact 内容信息读取
     * Operate 说明：
     * r:读取操作 操作方式：r
     * rw:读写操作 操作方式：r+
     * sr: 数据结构读取操作 操作对应函数file
     * rr: 读取全文 调用对应函数 file_get_contents
     */
    function read($file,$operate='r',$throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        # 判断错误编号是否为初始状态
        # 调用路径文件验证
        if(!is_file($_folder = replace(ROOT.DS.$file))){
            switch ($operate) {
                case 'sr': # 序列化读取
                    $_receipt = file($_folder);
                    break;
                case 'rw': # 读写
                    $_receipt = fopen($_folder, 'r+');
                    break;
                case 'rr': # 写入
                    $_receipt = file_get_contents($_folder, false);
                    break;
                case 'r': # 读取
                default: # 默认状态与读取状态一致
                    $_receipt = fopen($_folder, 'r');
                    break;
            }
        }else{
            $this->Error = "The file is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("File Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $file 文件路径
     * @param string $operate 操作类型
     * @param string $msg 写入值
     * @param boolean $throw 捕捉异常
     * @return mixed
     * @contact 内容信息更新
     * Operate 说明：
     * w：写入操作 操作方式：w
     * lw：前写入 操作方式：w+
     * bw：后写入 操作方式：a
     * fw：补充写入 操作方式：a+
     * re：重写 调用对应函数 file_put_contents
     */
    function write($file,$operate='r',$msg=null,$throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        # 判断错误编号是否为初始状态
        # 调用路径文件验证
        if(is_file($_folder = replace(ROOT.DS.$file))){
            # 未发生错误执行
            switch ($operate) {
                case 'w': # 写入
                    $_write = fopen($_folder, 'w');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 'lw': # 写入
                    $_write = fopen($_folder, 'w+');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 'bw': # 写入
                    $_write = fopen($_folder, 'a');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 'fw': # 写入
                    $_write = fopen($_folder, 'a+');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 're': # 写入
                default: # 默认状态与读取状态一致
                    $_receipt = file_put_contents($_folder, strval($msg));
                    break;
            }
        }else{
            $this->Error = "The file is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("File Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $file 文件夹地址
     * @return mixed
     * @context 获取文件夹信息
     */
    function get($file)
    {
        $_receipt = null;
        if(file_exists($_file = replace(ROOT.DS.$file))){
            $_receipt = array(
                "file_name" => $file,
                "file_size" => filesize($_file),
                "file_type" => filetype($_file),
                "file_change_time" => filectime($_file),
                "file_access_time" => fileatime($_file),
                "file_move_time" => filemtime($_file),
                "file_owner" => fileowner($_file),
                "file_limit" => fileperms($_file),
                "file_read" => is_readable($_file),
                "file_write" => is_writable($_file),
                "file_execute" => is_executable($_file),
                "file_create_type" => is_uploaded_file($_file)?"online":"location",
            );
        }
        return $_receipt;
    }
}