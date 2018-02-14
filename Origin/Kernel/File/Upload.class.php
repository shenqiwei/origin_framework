<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.File.File *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/06/19 22:07
 * update Time: 2017/06/19 22:07
 * chinese Context: IoC 上传模块封装
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
            try{
                throw new \Exception('Upload file input is invalid!');
            }catch(\Exception $e){
                echo("<br />");
                echo('Directory Error:'.$e->getMessage());
                exit(0);
            }
        }else{
            if(!is_null($type)){
                if(is_array($type)){
                    $this->_Suffix = key_exists($_FILES[$this->_Input_Name]['type'],$this->_Type_Array)?
                        $this->_Type_Array[$_FILES[$this->_Input_Name]['type']]:$_FILES[$this->_Input_Name]['type'];
                    if(!in_array($this->_Suffix,$type)){
                        try{
                            throw new \Exception('Files type is invalid!');
                        }catch(\Exception $e){
                            echo("<br />");
                            echo('Directory Error:'.$e->getMessage());
                            exit(0);
                        }
                    }
                }else{
                    if($_FILES[$this->_Input_Name]['type'] != strval($type)){
                        try{
                            throw new \Exception('Files type is invalid!');
                        }catch(\Exception $e){
                            echo("<br />");
                            echo('Directory Error:'.$e->getMessage());
                            exit(0);
                        }
                    }
                }
            }else{
                try{
                    throw new \Exception('Undefined file type!');
                }catch(\Exception $e){
                    echo("<br />");
                    echo('Directory Error:'.$e->getMessage());
                    exit(0);
                }
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
            try{
                throw new \Exception('Upload file input is invalid!');
            }catch(\Exception $e){
                echo("<br />");
                echo('Directory Error:'.$e->getMessage());
                exit(0);
            }
        }else{
            if(!is_null($size)){
                if(!empty(intval($size))){
                    if($_FILES[$this->_Input_Name]['size'] > intval($size)){
                        try{
                            throw new \Exception('Files size greater than defined value!');
                        }catch(\Exception $e){
                            echo("<br />");
                            echo('Directory Error:'.$e->getMessage());
                            exit(0);
                        }
                    }
                }else{
                    try{
                        throw new \Exception('Files size value is invalid!');
                    }catch(\Exception $e){
                        echo("<br />");
                        echo('Directory Error:'.$e->getMessage());
                        exit(0);
                    }
                }
            }else{
                try{
                    throw new \Exception('Undefined file size!');
                }catch(\Exception $e){
                    echo("<br />");
                    echo('Directory Error:'.$e->getMessage());
                    exit(0);
                }
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
            try{
                throw new \Exception('Upload file input is invalid!');
            }catch(\Exception $e){
                echo("<br />");
                echo('Directory Error:'.$e->getMessage());
                exit(0);
            }
        }else{
            if(!is_null($guide)){
                if(strpos($guide,SLASH)){
                    $_guide = explode(SLASH,$guide);
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
                                $this->_Save_Add .= SLASH.$_guide[$_i];
                            }
                        }
                    }
                }
            }
            if(is_bool($mark) and $mark === true){
                if(!is_null($this->_Save_Add)){
                    $this->_Save_Add .= SLASH.date('Ymd',time());
                }else{
                    $this->_Save_Add .= date('Ymd',time());
                }

            }
            $_files = new UploadEx();
            if(!is_null($_files->resource(__UPLOAD__.'/'.$this->_Save_Add))){
                $_files->manage(__UPLOAD__.'/'.$this->_Save_Add,'full',null);
            }
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param boolean $custom 上传文件类型
     * @return mixed
     */
    function update($custom=true)
    {
        if(is_null($this->_Input_Name)){
            try{
                throw new \Exception('Upload file input is invalid!');
            }catch(\Exception $e){
                echo("<br />");
                echo('Directory Error:'.$e->getMessage());
                exit(0);
            }
        }else{
            if(is_bool($custom) and $custom === true){
                $_file_name = $_FILES[$this->_Input_Name]['name'];
            }else{
                $_file_name = date('YmdHis',time()).'.'.$this->_Suffix;
            }
            if(!move_uploaded_file($_FILES[$this->_Input_Name]['tmp_name'],
                $this->_Dir.SLASH.__UPLOAD__.SLASH.$this->_Save_Add.SLASH.$_file_name)){
                try{
                    throw new \Exception('Files upload failed!');
                }catch(\Exception $e){
                    echo("<br />");
                    echo('Directory Error:'.$e->getMessage());
                    exit(0);
                }
            }
        }
        return $this->_Save_Add.SLASH.$_file_name;
    }

}