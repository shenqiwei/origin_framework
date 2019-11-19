<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.3
 * @copyright 2015-2017
 * @context: IoC 文件操作封装
 */
namespace Origin\Kernel\File;

use Origin\Kernel\Parameter\Output;
use Exception;

class File
{
    /**
     * @access protected
     * @var object $_object
     * @contact 实例化对象
     */
    private $_Object = null;
    /**
     * 对象文件夹信息
     * @var string $_Dir
     */
    private $_Dir = null;
    /**
     * 导引路径信息
     * @var string $_Guide
     */
    private $_Guide = null;
    /**
     * @access public
     * @contact 构造函数
     */
    function __construct()
    {
    }
    /**
     * 回传类对象信息
     * @access public
     * @param object $object
     */
    function __setFile($object)
    {
        $this->_Object = $object;
    }
    /**
     * 获取类对象信息,仅类及其子类能够使用
     * @access public
     * @return object
     */
    protected function __getFile()
    {
        return $this->_Object;
    }
    /**
     * @access public
     * @param string $guide 文件路径
     * @return null/boolean/object
     * @content 检测文件路径索引结构完整度
     */
    function resource($guide=null)
    {
        # 初始化根目录位置，错误信息和编码
        $this->_Dir = ROOT;
        # 设置返回对象
        $_receipt = $this->_Object;
        # 当对象地址索引为null
        $_uri = $this->_Guide;
        # 创建对象索引变量
        if(!is_null($guide)){
            $_uri = $guide;
        }else{
            if(is_null($_uri)){
                $_output = new Output();
                $_output->exception("File Error",'Files guide is invalid!',debug_backtrace(0,1));
                exit();
            }
        }
        # 判断错误编号是否为初始状态
        $_guide = explode('/',$_uri);
        $_u = 0;
        foreach($_guide as $_uri){
            if($_u === 0){
                $this->_Dir .= $_uri;
            }else{
                $this->_Dir .= DS.$_uri;
            }

            if(strpos($_uri,'.')){
                # 判定对象是否为文件
                if(!is_file($this->_Dir)){
                    if(!is_null($guide)) $_receipt = $_uri;
                    break;
                }
            }else{
                # 判断对象是否为文件夹
                if(!is_dir($this->_Dir)){
                    if(!is_null($guide)) $_receipt = $_uri;
                    break;
                }
            }
            $_u ++;
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $guide 文件路径
     * @param string $operate 执行操作
     * @param string $name 重命名
     * @return null/object
     * @content 对文件夹进行操作
     * Operate 说明：
     * create: 创建目标目录，如果目录不全，就停止创建并返回错误信息
     * full: 创建并补全目录，如创建失败则返回错误信息
     * rename：修改目标目录名（重命名），如果删除失败则停止并返回错误信息
     * remove：删除目标目录空目录，如果删除失败则停止并返回错误信息
     * Type 说明：
     * folder: 对文件夹操作
     * [^folder]: 对文件操作
     */
    function manage($guide=null,$operate='full',$name=null)
    {
        # 设置返回对象
        $_receipt = $this->_Object;
        # 当对象地址索引为null
        $_uri = $this->_Guide;
        # 创建对象索引变量
        if(!is_null($guide)){
            $_uri = $guide;
        }else{
            if(is_null($_uri)){
                try{
                    throw new Exception('Files guide is invalid!');
                }catch(Exception $e){
                    $_output = new Output();
                    $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        # 判断错误编号是否为初始状态
        # 调用路径文件验证
        $_resource = $this->resource($_uri);
        # 判断返回值基本类型
        if (!is_object($_resource)) {
            # 转化对象类型
            $_guide = explode('/', $_uri);
            if (!is_null($_resource)) {
                $_create_status = null;
                # 初始化根目录位置，错误信息和编码
                $this->_Dir = ROOT;
                # 根据操作状态执行不同的操作，修改，删除，清楚在特定条件下，将不执行，只返回错误信息
                for ($_i = 0; $_i < count($_guide); $_i++) {
                    if($_i === 0){
                        $this->_Dir .= $_guide[$_i];
                    }else{
                        $this->_Dir .= DS . $_guide[$_i];
                    }
                    # 确定文件路径断点位置，并修改操作状态值
                    if (($operate === 'full' and ($_guide[$_i] == $_resource or !is_null($_create_status))) or
                        ($operate === 'create' and $_resource === $_guide[count($_guide) - 1])) {
                        # 当操作模式为补全创建时，条件结构才成立
                        if ($operate === 'full') {
                            $_create_status = 'execute';
                        }
                        if (strpos($this->_Dir, '.')) {
                            if ($_file = fopen($this->_Dir, 'w')) {
                                fclose($_file);
                            } else {
                                # 错误代码：00101，错误信息：文件创建失败
                                try{
                                    throw new Exception('Create folder[' . $_guide[$_i] . '] failed');
                                }catch(Exception $e){
                                    $_output = new Output();
                                    $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                                    exit();
                                }
                            }
                        } else {
                            # 创建对象文件夹，并赋予最高控制权限，该结构在windows默认生效
                            if (!mkdir($this->_Dir, 0777)) {
                                # 错误代码：00101，错误信息：文件创建失败
                                try{
                                    throw new Exception('Create folder[' . $_guide[$_i] . '] failed');
                                }catch(Exception $e){
                                    $_output = new Output();
                                    $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                                    exit();
                                }
                            }
                        }
                    } else {
                        continue;
                    }
                }
            } else {
                # 重命名，删除执行
                switch ($operate) {
                    case 'rename':
                        # 执行重命名操作
                        # 获取需重命名对象信息
                        $_object = $_guide[count($_guide) - 1];
                        # 判断是否有文件结构标记
                        if (strpos($_object, '.')) {
                            # 转换数据状态，拆分文件结构
                            $_arr = explode('.', $_object);
                            if (strpos($name, '.')) {
                                # 根据结构进行预处理
                                $_name_arr = explode('.', $name);
                                $name = $_name_arr[0] . '.' . $_arr[1];
                            } else {
                                $name .= '.' . $_arr[1];
                            }
                        }
                        # 装载整体结构
                        $_change = str_replace($_object, $name, $this->_Dir . DS . $this->_Guide);
                        if (!rename($this->_Dir, $_change)) {
                            # 错误代码：00102，错误信息：文件重命名失败
                            try{
                                throw new Exception('Files[' . $this->_Guide . '] rename failed');
                            }catch(Exception $e){
                                $_output = new Output();
                                $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                                exit();
                            }
                        }
                        break;
                    case 'remove':
                        # 执行删除
                        if (strpos($this->_Guide, '.')) {
                            if (!unlink($this->_Dir . DS . $this->_Guide)) {
                                try{
                                    throw new Exception('Remove file[' . $this->_Guide . '] failed');
                                }catch(Exception $e){
                                    $_output = new Output();
                                    $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                                    exit();
                                }
                            }else
                                $_receipt= true;
                        } else {
                            if (!rmdir($this->_Dir . DS . $this->_Guide)) {
                                try{
                                    throw new Exception('Remove folder[' . $this->_Guide . '] failed');
                                }catch(Exception $e){
                                    $_output = new Output();
                                    $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                                    exit();
                                }
                            }else
                                $_receipt = true;
                        }
                        break;
                    default:
                        # 错误代码：00100，错误信息：文件已创建
                        try{
                            throw new Exception('Folder has been created');
                        }catch(Exception $e){
                            $_output = new Output();
                            $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                        break;
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $guide 文件路径
     * @param string $operate 操作类型
     * @param string $msg 写入值
     * @return mixed
     * @contact 内容信息更新
     * Operate 说明：
     * r:读取操作 操作方式：r
     * rw:读写操作 操作方式：r+
     * sr: 数据结构读取操作 操作对应函数file
     * w：写入操作 操作方式：w
     * lw：前写入 操作方式：w+
     * cw:缺失创建并写入 调用对应函数manage，操作方式：w+
     * bw：后写入 操作方式：a
     * fw：补充写入 操作方式：a+
     * rr: 读取全文 调用对应函数 file_get_contents
     * re：重写 调用对应函数 file_put_contents
     */
    function write($guide=null,$operate='r',$msg=null)
    {
        # 设置返回对象
        $_receipt = $this->_Object;
        # 当对象地址索引为null
        $_uri = $this->_Guide;
        # 创建对象索引变量
        if(!is_null($guide)){
            $_uri = $guide;
        }else{
            if(is_null($_uri)){
                try{
                    throw new Exception('Files guide is invalid!');
                }catch(Exception $e){
                    $_output = new Output();
                    $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        # 判断错误编号是否为初始状态
        # 调用路径文件验证
        $_resource = $this->resource($_uri);
        # 判断返回值基本类型
        if (!is_object($_resource)) {
            if(!is_null($_resource)){
                if ($operate === 'cw' or $operate == 'fw') {
                    $_resource = $this->manage($_uri, 'full');
                } else {
                    try{
                        throw new Exception('Not Found Object File ' . $_resource);
                    }catch(Exception $e){
                        $_output = new Output();
                        $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }
        }
        # 未发生错误执行
        if (is_object($_resource) or is_null($_resource)) {
            switch ($operate) {
                case 'sr': # 序列化读取
                    $_receipt = file($this->_Dir);
                    break;
                case 'rw': # 读写
                    $_receipt = fopen($this->_Dir, 'r+');
                    break;
                case 'w': # 写入
                    $_write = fopen($this->_Dir, 'w');
                    if ($_write) {
                        fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 'lw': # 写入
                case 'cw': # 写入
                    $_write = fopen($this->_Dir, 'w+');
                    if ($_write) {
                        fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 'bw': # 写入
                    $_write = fopen($this->_Dir, 'a');
                    if ($_write) {
                        fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 'fw': # 写入
                    $_write = fopen($this->_Dir, 'a+');
                    if ($_write) {
                        fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case 'rr': # 写入
                    if (!file_get_contents($this->_Dir, false)) {
                        try{
                            throw new Exception('File read failed!');
                        }catch(Exception $e){
                            $_output = new Output();
                            $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }else
                        $_receipt = true;
                    break;
                case 're': # 写入
                    if (!file_put_contents($this->_Dir, strval($msg))) {
                        try{
                            throw new Exception("File Error",'File write failed!');
                        }catch(Exception $e){
                            $_output = new Output();
                            $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }else
                        $_receipt = true;
                    break;
                case 'r': # 读取
                default: # 默认状态与读取状态一致
                    $_receipt = fopen($this->_Dir, 'r');
                    break;
            }
        }
        return $_receipt;
    }
}