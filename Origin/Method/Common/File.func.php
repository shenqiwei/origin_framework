<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: Zero.Snake.Method.Function *
 * version: 1.0 *
 * structure: common framework *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2018/02/13 00:16
 * update Time: 2018/02/13 00:16
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 * @contact 文件操作主函数
 */
# 启用对象
use Origin\Kernel\File\File as FileClass;
/**
 * @access public
 * @param string $uri 文件地址路径
 * @return mixed
 * @contact
 */
function indexFiles($uri)
{
    if(formatFile($uri)){
        # 实例化文件类对象
        $_files = new FileClass();
        # 调用结构验证方法
        try{
            if(is_null($_files->resource($uri))){
                $_receipt = true;
            }else{
                $_receipt = false;
            }
        }catch (Exception $e){
            $_receipt = $e->getMessage();
        }
    }else{
        $_receipt = 'Uri['.$uri.'] format is invalid';
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $uri 文件地址路径
 * @param boolean $full 是否补全地址缺失部分
 * @return mixed
 * @contact
*/
function createFiles($uri,$full=false)
{
    if(formatFile($uri)){
        # 实例化文件类对象
        $_files = new FileClass();
        # 操作类型变量
        $_operate = 'create';
        # 当需要补全结构时
        if($full === true){
            $_operate = 'full';
        }
        # 调用结构验证方法
        try{
            $_files->manage($uri,$_operate);
            $_receipt = true;
        }catch (Exception $e){
            $_receipt = $e->getMessage();
        }
    }else{
        $_receipt = 'Uri['.$uri.'] format is invalid';
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $uri 文件地址路径
 * @param string $new_name 替换文件名
 * @return mixed
 * @contact
 */
function renameFiles($uri,$new_name)
{
    if(formatFile($uri)){
        # 验证新命名是否符合规则
        if(nameFile($new_name)){
            # 实例化文件类对象
            $_files = new FileClass();
            # 调用结构验证方法
            try{
                $_files->manage($uri,'rename',$new_name);
                $_receipt = true;
            }catch (Exception $e){
                $_receipt = $e->getMessage();
            }
        }else{
            $_receipt = 'New name['.$new_name.'] format is invalid';
        }
    }else{
        $_receipt = 'Uri['.$uri.'] format is invalid';
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $uri 文件地址路径
 * @return mixed
 * @contact
 */
function removeFile($uri)
{
    if(formatFile($uri)){
        # 实例化文件类对象
        $_files = new FileClass();
        # 调用结构验证方法
        try{
            $_files->manage($uri,'remove');
            $_receipt = true;
        }catch (Exception $e){
            $_receipt = $e->getMessage();
        }
    }else{
        $_receipt = 'Uri['.$uri.'] format is invalid';
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $uri 文件地址路径
 * @param string $type 文件读取方式
 * @return mixed
 * @contact
 */
function readToFile($uri,$type='r')
{
    if(formatFile($uri)){
        # 实例化文件类对象
        $_files = new FileClass();
        # 操作类型变量
        $_operate = 'r';
        if(in_array($type,array('r','rw','sr','rr'))){
            $_operate = $type;
        }
        # 调用结构验证方法
        try{
            $_files->write($uri,$_operate);
            $_receipt = true;
        }catch (Exception $e){
            $_receipt = $e->getMessage();
        }
    }else{
        $_receipt = 'Uri['.$uri.'] format is invalid';
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $uri 文件地址路径
 * @param string $msg 写入值
 * @param string $type 文件写入方式
 * @return mixed
 * @contact
 */
function writeInFile($uri,$msg,$type='w')
{
    if(formatFile($uri)){
        # 实例化文件类对象
        $_files = new FileClass();
        # 操作类型变量
        $_operate = 'w';
        if(in_array($type,array('w','lw','cw','bw','fw','re'))){
            # 调用结构验证方法
            try{
                $_files->write($uri,$_operate,$msg);
                $_receipt = true;
            }catch (Exception $e){
                $_receipt = $e->getMessage();
            }
        }else{
            $_receipt = 'Operation type is invalid';
        }
    }else{
        $_receipt = 'Uri['.$uri.'] format is invalid';
    }
    return $_receipt;
}
