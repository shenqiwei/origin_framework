<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin上传模块封装 (重构)
 */
namespace Origin\Package;

class Upload
{
    /**
     * @access private
     * @var string $Input 表单名
     */
    private $Input;

    /**
     * @access private
     * @var int $Size 上传大小限制
     */
    private $Size = 0;

    /**
     * @access private
     * @var array $Type 上传类型限制
     */
    private $Type = array();

    /**
     * @access private
     * @var string $Store 存储位置
     */
    private $Store;

    /**
     * @access private
     * @var string $Error 错误信息
     */
    private $Error;

    /**
     * @access private
     * @var array $TypeArray 文件扩展名比对数组
     */
    private $TypeArray = array(
        'text/plain' => 'txt',
        'application/vnd.ms-excel' =>  'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'text/html' => 'html',
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
    );

    /**
     * @access public
     * @param string $input 表单名称 form type is 'multipart/form-data' 该结构有效
     * @param array $type 上传文件类型
     * @param int $size 上传文件大小，默认值 0
     * @param string|null $guide 上传文件存储路径
     * @return void
     * @context 上传条件设置函数
     */
    function condition($input, $type, $size=0,$guide=null)
    {
        # 对条件变量进行赋值
        $this->Input = $input;
        $this->Type = $type;
        if(!empty(intval($size)))
            $this->Size = $size;
        # 重置上传组件异常信息
        $this->Error = null;
        if(!is_null($this->Store) and !is_null($guide))
            $this->Store = replace($guide);
        else
            $this->Store = $guide;
    }

    /**
     * 执行上传，上传成功后返回上传文件相对路径信息
     * @access public
     * @return boolean|string 返回上传结果或失败状态
     */
    function update()
    {
        $receipt = false;
        # 存储目录
        $dir = date("Ymd",time());
        # 验证存储主目录是否有效
        if(!isset($this->Store) or is_null($this->Store))
            $this->Store = replace("resource/upload/".$dir);
        else
            $this->Store .= $dir;
        if(!is_dir(replace(ROOT.DS.$this->Store))){
            $file = new Folder();
            $file->create(str_replace(DS, "/", $this->Store), true);
        }
        if(!$this->Input)
            $this->Error = "Upload file input is invalid";
        else{
            $file = $_FILES[$this->Input];
            if(is_array($file["name"])){
                $folder = array();
                for($i = 0;$i < count($file["name"]);$i++){
                    if(key_exists($file["type"][$i],$this->TypeArray))
                        $suffix = $this->TypeArray[$file["type"][$i]];
                    if(!isset($suffix)){
                        $suffix = explode(".",$file["name"][$i])[1];
                    }
                    if(isset($suffix)){
                        if(!empty($this->Type)){
                            if(!in_array($suffix,$this->Type))
                                $this->Error = "Files type is invalid";
                        }
                    }else{
                        $this->Error = "Files type is invalid";
                    }
                    if(is_null($this->Error)){
                        if($this->Size and $file["size"][$i] > $this->Size)
                            $this->Error = "Files size greater than defined value";
                    }
                    if(is_null($this->Error)){
                        $upload_file = sha1($file["tmp_name"][$i]).time().".".$suffix;
                        if(move_uploaded_file($file['tmp_name'][$i],
                            replace(ROOT.DS.$this->Store).DS.$upload_file)){
                            array_push($folder,$dir."/".$upload_file);
                        }else{
                            $this->Error = "Files upload failed";
                            break;
                        }
                    }
                }
                $receipt = $folder;
            }else{
                if(key_exists($file["type"],$this->TypeArray))
                    $suffix = $this->TypeArray[$file["type"]];
                if(!isset($suffix)){
                    $suffix = explode(".",$file["name"])[1];
                }
                if(isset($suffix)){
                    if(!is_null($this->Type)){
                        if(!in_array($suffix,$this->Type))
                            $this->Error = "Files type is invalid";
                    }
                }else{
                    $this->Error = "Files type is invalid";
                }
                if(is_null($this->Error)){
                    if($this->Size and $file["size"] > $this->Size)
                        $this->Error = "Files size greater than defined value";
                }
                if(is_null($this->Error)){
                    $upload_file = sha1($file["tmp_name"]).time().".".$suffix;
                    if(move_uploaded_file($file['tmp_name'],
                        replace(ROOT.DS.$this->Store).DS.$upload_file)){
                        $receipt = $dir."/".$upload_file;
                    }else{
                        $this->Error = "Files upload failed";
                    }
                }
            }
        }
        return $receipt;
    }

    /**
     * 获取错误信息
     * @access public
     * @return string|null 返回异常信息
     */
    function getError()
    {
        return $this->Error;
    }
}