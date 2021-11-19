<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2017
 */
/**
 * 日志写入
 * @access public
 * @param string $folder 日志路径
 * @param string $context 日志模板
 * @return  boolean 返回执行结果状态
 */
function _log(string $folder, string $context): bool
{
    $receipt = false;
    logWrite:
    # 使用写入方式进行日志创建创建和写入
    $handle = fopen(ROOT.DS.replace($folder),"a");
    if($handle){
        # 执行写入操作，并返回操作回执
        $receipt = fwrite($handle,$context);
        # 关闭文件源
        fclose($handle);
    }else{
        if(!file_exists(ROOT.DS.replace($folder))){
            $dir = explode(DS,$folder);
            $new = null;
            for($i = 0;$i < count($dir)-1;$i++){
                $new .= DS.$dir[$i];
                if(!is_dir(ROOT.DS.$new))
                    mkdir(ROOT.DS.$new);
            }
            goto logWrite;
        }
    }
    return $receipt;
}

/**
 * 异常记录日志 error log
 * @access public
 * @param string $msg 日志模板信息
 * @return bool 返回执行结果状态
 */
function errorLog(string $msg): bool
{
    $uri = LOG_EXCEPTION.date('Ymd').'.log';
    $model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return _log($uri,$model_msg);
}