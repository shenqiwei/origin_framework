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
     * 操作常量
     * @access public
    */
    const FILE_READ = "r";
    const FILE_READ_WRITE = "rw";
    const FILE_SEQ_READ = "sr";
    const FILE_CONTENT_READ = "cr";
    const FILE_WRITE = "w";
    const FILE_LEFT_WRITE = "lw";
    const FILE_BEHIND_WRITE = "bw";
    const FILE_FULL_WRITE = "fw";
    const FILE_CONTENT_WRITE = "cw";

    /**
     * 创建文件夹
     * @access public
     * @param string $folder 文件地址
     * @param boolean $autocomplete 自动补全完整路径，默认值 false
     * @param boolean $throw 捕捉异常
     * @return boolean 返回执行结果状态值
     */
    function create(string $folder, bool $autocomplete=false, bool $throw=false): bool
    {
        # 设置返回对象
        $receipt = false;
        $file = replace($this->Root.DS.$folder);
        # 判断文件夹是否已创建完成
        if(is_file($file))
            $receipt = true;
        else{
            if($handle = fopen($file, 'w')){
                $receipt = true;
                fclose($handle);
            }else{
                # 获取文件夹信息
                $dir = substr($folder,0,strrpos($folder,"/"));
                # 调用父类create方法
                if(parent::create($dir, $autocomplete, true)){
                    if($handle = fopen($file, 'w')){
                        $receipt = true;
                        fclose($handle);
                    }
                }
            }
            if(!$receipt){
                # 错误代码：00101，错误信息：文件创建失败
                $this->Error = "Create file [$folder] failed";
                try {
                    throw new Exception($this->Error);
                } catch (Exception $e) {
                    exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
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
    function remove(string $folder, $throw=false): bool
    {
        # 设置返回对象
        $receipt = false;
        $file = replace($this->Root.DS.$folder);
        if(!is_file($file))
            $receipt = true;
        else{
            if(!unlink($file)) {
                $this->Error = "Remove file [$folder] failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
                    }
                }
            }else
                $receipt = true;
        }
        return $receipt;
    }

    /**
     * 文件重命名
     * @access public
     * @param string $folder 文件地址
     * @param string $name 新名称
     * @param boolean $throw 捕捉异常
     * @return boolean 返回执行结果状态值
     */
    function rename(string $folder, string $name, $throw=false): bool
    {
        # 设置返回对象
        $receipt = false;
        if(is_file($file = replace($this->Root.DS.$folder))){
            if (!rename($file, $name)) {
                # 错误代码：00102，错误信息：文件重命名失败
                $this->Error = "File [$folder] rename failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
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
                }
            }
        }
        return $receipt;
    }

    /**
     * 内容信息读取
     * Operate 说明：
     * r:读取操作 操作方式：r
     * rw:读写操作 操作方式：r+
     * sr: 数据结构读取操作 操作对应函数file
     * cr: 读取全文 调用对应函数 file_get_contents
     * @access public
     * @param string $folder 文件路径
     * @param string $operate 操作类型
     * @param int $size 限定读取大小
     * @param boolean $throw 捕捉异常
     * @return string|false 返回文件内容或失败状态
     */
    function read(string $folder, string $operate=self::FILE_READ, int $size=0, bool $throw=false)
    {
        # 设置返回对象
        $receipt = false;
        # 判断错误编号是否为初始状态
        # 调用路径文件验证
        if(is_file($file = replace($this->Root.DS.$folder))){
            switch ($operate) {
                case self::FILE_SEQ_READ: # 序列化读取
                    $receipt = file($file);
                    break;
                case self::FILE_READ_WRITE: # 读写
                    $handle = fopen($file, 'r+');
                    $receipt = fread($handle,($size > 0)?$size:filesize($folder));
                    break;
                case self::FILE_CONTENT_READ: # 写入
                    $receipt = file_get_contents($file, false);
                    break;
                case self::FILE_READ: # 读取
                default: # 默认状态与读取状态一致
                    $handle = fopen($file, 'r');
                    $receipt = fread($handle,($size > 0)?$size:filesize($folder));
                    break;
            }
        }else{
            $this->Error = "The file is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("File Error",$e->getMessage(),debug_backtrace(0,1));
                }
            }
        }
        return $receipt;
    }

    /**
     * 内容信息更新
     * Operate 说明：
     * w：写入操作 操作方式：w
     * lw：前写入 操作方式：w+
     * bw：后写入 操作方式：a
     * fw：补充写入 操作方式：a+
     * cw：重写 调用对应函数 file_put_contents
     * @access public
     * @param string $folder 文件路径
     * @param string $operate 操作类型
     * @param string|null $msg 写入值
     * @return boolean 返回写入结果状态值
     */
    function write(string $folder, string $operate=self::FILE_WRITE, ?string $msg=null): bool
    {
        # 设置返回对象
        $receipt = false;
        # 未发生错误执行
        $file = replace($this->Root.DS.$folder);
        switch ($operate) {
            case self::FILE_WRITE: # 写入
                $write = fopen($file, 'w');
                if ($write) {
                    $receipt = fwrite($write, $msg);
                    fclose($write);
                }
                break;
            case self::FILE_LEFT_WRITE: # 写入
                $write = fopen($file, 'w+');
                if ($write) {
                    $receipt = fwrite($write, $msg);
                    fclose($write);
                }
                break;
            case self::FILE_BEHIND_WRITE: # 写入
                $write = fopen($file, 'a');
                if ($write) {
                    $receipt = fwrite($write, $msg);
                    fclose($write);
                }
                break;
            case self::FILE_FULL_WRITE: # 写入
                $write = fopen($file, 'a+');
                if ($write) {
                    $receipt = fwrite($write, $msg);
                    fclose($write);
                }
                break;
            case self::FILE_CONTENT_WRITE: # 写入
            default: # 默认状态与读取状态一致
                $receipt = file_put_contents($file, $msg);
                break;
        }
        return $receipt;
    }

    /**
     * 获取文件夹信息
     * @access public
     * @param string $folder 文件夹地址
     * @return array 返回文件信息或失败状态
     */
    function get(string $folder): ?array
    {
        if(file_exists($file = replace($this->Root.DS.$folder))){
            $receipt = array(
                "file_name" => $folder,
                "file_size" => filesize($file),
                "file_type" => filetype($file),
                "file_change_time" => filectime($file),
                "file_access_time" => fileatime($file),
                "file_move_time" => filemtime($file),
                "file_owner" => fileowner($file),
                "file_limit" => fileperms($file),
                "file_read" => is_readable($file),
                "file_write" => is_writable($file),
                "file_execute" => is_executable($file),
                "file_create_type" => is_uploaded_file($file)?"online":"location",
            );
        }
        return $receipt ?? null;
    }
}