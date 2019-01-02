<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/1/1
 * Time: 11:45
 */

namespace Origin\Kernel\File;


class Upload
{
    /**
     * @access protected
     * @var string $_Uri
     * @contact 原始路径变量
     */
    protected $_Dir = ROOT;
    /**
     * @access protected
     * @var object $_Object
     * @contact 实例化对象
     */
    protected $_Object = null;
    /**
     * @access protected
     * @var string $_Input_Name
     * @contact 表单名称
     */
    protected $_Input_Name = null;
    /**
     * @access protected
     * @var array $_Input_type
     * @contact 上传文件类型约束
    */
    protected $_Input_Type = null;
    /**
     * @access protected
     * @param int $_Input_Size
     * @contact 文件大小约束
    */
    protected $_Input_Size = 0;
    /**
     * @access protected
     * @var string $_Save_Add
     * @contact 存储地址
     */
    protected $_Save_Add = null;
    /**
     * @access protected
     * @var string $_Error_Msg
     * @contact 错误信息
     */
    protected $_Error_Msg = null;
    /**
     * @access protected
     * @var array $_Type_Array
     * @contact 文件扩展名比对数组
     */
    protected $_Type_Array = array(
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
        'image/bmp' => 'png',
    );
    /**
     * @access private
     * @var string $_Suffix
     * @contact 文件扩展名
     */
    protected $_Suffix = null;
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
     * @param array $type 上传文件类型
     * @return boolean|object|null
     */
    function type($type)
    {
        if(is_array($type)){
            $this->_Input_Type = $type;
        }else{
            if(!is_null($type) and !empty($type))
                array_push($this->_Input_Type,$type);
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param int $size 上传文件大小
     * @return boolean|object|null
     */
    function size($size=0)
    {
        $this->_Input_Size = abs(intval($size));
        return $this->_Object;
    }
    /**
     * @access public
     * @param string $guide 上传文件存储路径
     * @param boolean $mark 是否使用时间标记存储目录默认使用
     * @return boolean|object|null
     */
    function path($guide=null,$mark=true)
    {
        if (!is_null($guide)) {
            if (strpos($guide, SLASH)) {
                $_guide = explode(SLASH, $guide);
            } else {
                if (strpos($guide, '/')) {
                    $_guide = explode('/', $guide);
                }
            }
            if (isset($_guide)) {
                for ($_i = 0; count($_guide); $_i++) {
                    if ((empty($_guide[$_i]) and !is_numeric($_guide[$_i]) and is_null($_guide[$_i]))) {
                        continue;
                    } else {
                        if (is_null($this->_Save_Add)) {
                            $this->_Save_Add = $_guide[$_i];
                        } else {
                            $this->_Save_Add .= SLASH . $_guide[$_i];
                        }
                    }
                }
            }
        }
        if (is_bool($mark) and $mark === true) {
            if (!is_null($this->_Save_Add)) {
                $this->_Save_Add .= SLASH . date('Ymd', time());
            } else {
                $this->_Save_Add .= date('Ymd', time());
            }

        }
        $_files = new File();
        if (!is_null($_files->resource(__UPLOAD__ . '/' . $this->_Save_Add))) {
            $_files->manage(__UPLOAD__ . '/' . $this->_Save_Add, 'full', null);
        }
        return $this->_Object;
    }
    /**
     * @access public
     * @param boolean $custom 上传文件原始名
     * @return mixed
     * @context 单文件上传使用方法
     */
    function save($custom=true)
    {
        if(is_null($this->_Input_Name)){
            $this->_Error_Msg = "Upload file input is invalid!";
        }else{
            $_upload_file = $_FILES[$this->_Input_Name];
            if(is_array($_upload_file["name"])){
                for($_i = 0;$_i < count($_upload_file["name"]);$_i++){
                    if(!is_null($this->_Input_Type)){
                        if(in_array($_upload_file["type"][$_i],$this->_Type_Array)){
                            if(!in_array($_upload_file["type"][$_i],$this->_Input_Type)){
                                $this->_Error_Msg = "Upload file type is invalid";
                            }
                        }
                    }
                    if(is_null($this->_Error_Msg) and !empty($this->_Input_Size)){
                        if($this->_Input_Size <= $_upload_file["size"][$_i]){
                            $this->_Error_Msg = "Upload file size greater than set size ";
                        }
                    }
                    if(is_null($this->_Error_Msg)){
                        if(is_bool($custom) and $custom === true){
                            $_file_name = $_upload_file['name'][$_i];
                        }else{
                            $_file_name = date('YmdHis',time())."_{$_i}.".$this->_Suffix;
                        }
                        if(!move_uploaded_file($_upload_file['tmp_name'][$_i],
                            $this->_Dir.SLASH.__UPLOAD__.SLASH.$this->_Save_Add.SLASH.$_file_name)){
                            $this->_Error_Msg = "Files upload failed!";
                            errorLogs("File Upload Error :".$this->_Error_Msg."System Error Code：".$_FILES[$this->_Input_Name]["error"][$_i]." Pic Size:".$_FILES[$this->_Input_Name]['size'][$_i]." Pic name:".$_FILES[$this->_Input_Name]['name'][$_i]);
                        }
                    }
                }
            }else{
                if(!is_null($this->_Input_Type)){
                    if (in_array($_upload_file["type"], $this->_Type_Array)) {
                        if (!in_array($_upload_file["type"], $this->_Input_Type)) {
                            $this->_Error_Msg = "Upload file type is invalid";
                        }
                    }
                }
                if(is_null($this->_Error_Msg) and !empty($this->_Input_Size)){
                    if($this->_Input_Size <= $_upload_file["size"]){
                        $this->_Error_Msg = "Upload file size greater than set size ";
                    }
                }
                var_dump($_FILES);
                if(is_null($this->_Error_Msg)){
                    if(is_bool($custom) and $custom === true){
                        $_file_name = $_upload_file['name'];
                    }else{
                        $_file_name = date('YmdHis',time()).'.'.$this->_Suffix;
                    }
                    if(!move_uploaded_file($_upload_file['tmp_name'],
                        $this->_Dir.SLASH.__UPLOAD__.SLASH.$this->_Save_Add.SLASH.$_file_name)){
                        $this->_Error_Msg = "Files upload failed!";
                        errorLogs("File Upload Error :".$this->_Error_Msg."System Error Code：".$_FILES[$this->_Input_Name]["error"]." Pic Size:".$_FILES[$this->_Input_Name]['size']." Pic name:".$_FILES[$this->_Input_Name]['name']);
                    }
                }
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