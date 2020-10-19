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
     * @var string $_Input 表单名
     * @var int $_Size 上传大小限制
     * @var array $_Type 上传类型限制
     * @var string $_Store 存储位置
     */
    private $_Input = null;
    private $_Size = 0;
    private $_Type = array();
    private $_Store = null;
    /**
     * @access private
     * @var string $_Error_Msg
     * @contact 错误信息
     */
    private $_Error = null;
    /**
     * @access private
     * @var array $_Type_Array
     * @contact 文件扩展名比对数组
     */
    private $_Type_Array = array(
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
     */
     function input($input)
     {
         $this->_Input = $input;
     }
    /**
     * @access public
     * @param array $type 上传文件类型
     */
    function type($type=null)
    {
        if(!is_null($type))
            $this->_Type = $type;
    }
    /**
     * @access public
     * @param int $size 上传文件大小
     */
    function size($size=0)
    {
        if(!empty(intval($size)))
            $this->_Size = $size;
    }
    /**
     * @access public
     * @param string $guide 上传文件存储路径
     */
    function store($guide=null)
    {
        if(!is_null($guide))
            $this->_Store = replace($guide);
    }
    /**
     * @access public
     * @return boolean|string
     */
    function update()
    {
        $_receipt = false;
        # 存储目录
        $_dir = null;
        # 验证存储主目录是否有效
        if(is_null($this->_Store)){
            # 设置存储子目录，使用年月日拆分存储内容
            $_dir = date("Ymd",time());
            $this->_Store = replace("resource/upload/".$_dir);
        }
        if(!is_dir(replace(ROOT.$this->_Store))){
            $_file = new File();
            $_file->manage(str_replace(DS,"/",$this->_Store),"full");
        }
        if(!$this->_Input)
            $this->_Error = "Upload file input is invalid";
        else{
            $_file = $_FILES[$this->_Input];
            if(is_array($_file["name"])){
                $_folder = array();
                for($_i = 0;$_i < count($_file["name"]);$_i++){
                    if(key_exists($_file["type"][$_i],$this->_Type_Array))
                        $_suffix = $this->_Type_Array[$_file["type"][$_i]];
                    if(!isset($_suffix)){
                        $_suffix = explode(".",$_file["name"][$_i])[1];
                    }
                    if(isset($_suffix)){
                        if(!empty($this->_Type)){
                            if(!in_array($_suffix,$this->_Type))
                                $this->_Error = "Files type is invalid";
                        }
                    }else{
                        $this->_Error = "Files type is invalid";
                    }
                    if(is_null($this->_Error)){
                        if($this->_Size and $_file["size"][$_i] > $this->_Size)
                            $this->_Error = "Files size greater than defined value";
                    }
                    if(is_null($this->_Error)){
                        $_upload_file = sha1($_file["tmp_name"][$_i]).time().".".$_suffix;
                        if(move_uploaded_file($_file['tmp_name'][$_i],
                            replace(ROOT.$this->_Store).DS.$_upload_file)){
                            array_push($_folder,$_dir."/".$_upload_file);
                        }else{
                            $this->_Error = "Files upload failed";
                            break;
                        }
                    }
                }
                $_receipt = $_folder;
            }else{
                if(key_exists($_file["type"],$this->_Type_Array))
                    $_suffix = $this->_Type_Array[$_file["type"]];
                if(!isset($_suffix)){
                    $_suffix = explode(".",$_file["name"])[1];
                }
                if(isset($_suffix)){
                    if(!is_null($this->_Type)){
                        if(!in_array($_suffix,$this->_Type))
                            $this->_Error = "Files type is invalid";
                    }
                }else{
                    $this->_Error = "Files type is invalid";
                }
                if(is_null($this->_Error)){
                    if($this->_Size and $_file["size"] > $this->_Size)
                        $this->_Error = "Files size greater than defined value";
                }
                if(is_null($this->_Error)){
                    $_upload_file = sha1($_file["tmp_name"]).time().".".$_suffix;
                    if(move_uploaded_file($_file['tmp_name'],
                        replace(ROOT.$this->_Store).DS.$_upload_file)){
                        $_receipt = $_dir."/".$_upload_file;
                    }else{
                        $this->_Error = "Files upload failed";
                    }
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @return mixed
     */
    function getError()
    {
        return $this->_Error;
    }
}