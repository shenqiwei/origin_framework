<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2017
 * @context: IoC 上传模块封装
 */
namespace Origin\Kernel\File;

use Origin\Kernel\File\File as UploadEx;

class Upload
{
    /**
     * @access private
     * @var string $_Uri
     * @contact 原始路径变量
     */
    private $_Dir = ROOT;
    /**
     * @access private
     * @var object $_Object
     * @contact 实例化对象
     */
    private $_Object = null;
    /**
     * @access private
     * @var string $_Input_Name
     * @contact 表单名称
     */
    private $_Input_Name = null;
    /**
     * @access private
     * @var string $_Save_Add
     * @contact 存储地址
     */
    private $_Save_Add = null;
    /**
     * @access private
     * @var string $_Error_Msg
     * @contact 错误信息
    */
    private $_Error_Msg = "ERROR_0000";
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
     * @access private
     * @var string $_Suffix
     * @contact 文件扩展名
    */
    private $_Suffix = null;
    /**
     * 回传类对象信息
     * @access public
     * @param object $object
     */
    public function __setUpload($object)
    {
        $this->_Object = $object;
    }
    /**
     * 获取类对象信息,仅类及其子类能够使用
     * @access public
     * @return object
     */
    public function __getUpload()
    {
        return $this->_Object;
    }
    /**
     * @access public
     * @param string $input 表单名称 form type is 'multipart/form-data' 该结构有效
     * @contact 构造函数，用于对象结构装载
     */
    function __construct($input=null)
    {
        if(!is_null($input)){
            $this->_Input_Name = $input;
        }
    }
    /**
     * @access public
     * @param string $name 上传文件名称
     * @return boolean|object|null
     */
    function form($name)
    {
        if(!is_null($name)){
            $this->_Input_Name = $name;
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param string|array $type 上传文件类型
     * @return boolean|object|null
     */
    function type($type=null)
    {
        if(is_null($this->_Input_Name)){
            $this->_Error_Msg = "Upload file input is invalid!";
        }else{
            if(!is_null($type)){
                if(is_array($type)){
                    # 装载扩展名信息
                    $this->_Suffix = $_FILES[$this->_Input_Name]['type'];
                    # 判断文件类型信息是否存在特例内容
                    if(key_exists($this->_Suffix,$this->_Type_Array)){
                        $this->_Suffix = $this->_Type_Array[$_FILES[$this->_Input_Name]['type']];
                    }
                    # 判断扩展名是否符合设置要求
                    if(!in_array($this->_Suffix,$type)){
                        $this->_Error_Msg = "Files type is invalid!";
                    }
                }else{
                    if($_FILES[$this->_Input_Name]['type'] != strval($type)){
                        $this->_Error_Msg = "Files type is invalid!";
                    }
                }
            }else{
                $this->_Error_Msg = "Undefined file type!";
            }
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param int $size 上传文件大小
     * @return boolean|object|null
     */
    function size($size=null)
    {
        if(is_null($this->_Input_Name)){
            $this->_Error_Msg = "Upload file input is invalid!";
        }else{
            if(!is_null($size)){
                if(!empty(intval($size))){
                    if($_FILES[$this->_Input_Name]['size'] > intval($size)){
                        $this->_Error_Msg = "Files size greater than defined value!";
                    }
                }else{
                    $this->_Error_Msg = "Files size value is invalid!";
                }
            }else{
                $this->_Error_Msg = "Undefined file size!";
            }
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param string $guide 上传文件存储路径
     * @param boolean $mark 是否使用时间标记存储目录默认使用
     * @return boolean|object|null
     */
    function save($guide=null,$mark=true)
    {
        if(is_null($this->_Input_Name)){
            $this->_Error_Msg = "Upload file input is invalid!";
        }else{
            if(!is_null($guide)){
                if(strpos($guide,DS)){
                    $_guide = explode(DS,$guide);
                }else{
                    if(strpos($guide,'/')){
                        $_guide = explode('/',$guide);
                    }
                }
                if(isset($_guide)){
                    for($_i = 0;count($_guide);$_i++){
                        if((empty($_guide[$_i]) and !is_numeric($_guide[$_i]) and is_null($_guide[$_i]))){
                            continue;
                        }else{
                            if(is_null($this->_Save_Add)){
                                $this->_Save_Add = $_guide[$_i];
                            }else{
                                $this->_Save_Add .= DS.$_guide[$_i];
                            }
                        }
                    }
                }
            }
            if(is_bool($mark) and $mark === true){
                if(!is_null($this->_Save_Add)){
                    $this->_Save_Add .= DS.date('Ymd',time());
                }else{
                    $this->_Save_Add .= date('Ymd',time());
                }

            }
            $_files = new UploadEx();
            if(!is_null($_files->resource(__RESOURCE__.'/Public/'.$this->_Save_Add))){
                $_files->manage(__RESOURCE__.'/Public/'.$this->_Save_Add,'full',null);
            }
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param boolean $custom 上传文件原始名称
     * @return mixed
     */
    function update($custom=true)
    {
        if(is_null($this->_Input_Name)){
            $this->_Error_Msg = "Upload file input is invalid!";
        }else{
            if(is_bool($custom) and $custom === true){
                $_file_name = $_FILES[$this->_Input_Name]['name'];
            }else{
                $_file_name = time().str_replace('.','',str_replace(' ','',microtime())).'.'.$this->_Suffix;
            }
            if(!move_uploaded_file($_FILES[$this->_Input_Name]['tmp_name'],
                $this->_Dir.DS.__RESOURCE__.DS.'Public'.DS.$this->_Save_Add.DS.$_file_name)){
                $this->_Error_Msg = "Files upload failed!";
            }
        }
        if(isset($_file_name))
            return $this->_Save_Add.'/'.$_file_name;
        else
            return null;
    }
    /**
     * @access public
     * @return mixed
     */
    function getErrorMsg()
    {
        return $this->_Error_Msg;
    }
}