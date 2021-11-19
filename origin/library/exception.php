<?php
/**
 * 应用异常显示模块
 * @access public
 * @param string $title 异常标题
 * @param string|array $msg 异常信息数组
 * @param array $file 异常文件描述数组
 * @return void
 */
function exception(string $title, $msg, array $file)
{
    $error= array(
        "msg" => "$title [Error Code:0000-0] $msg",
        "file" => "{$file[0]["file"]}",
        "line" => "{$file[0]["line"]}",
        "function" => "{$file[0]["function"]}",
        "class" => "{$file[0]["class"]}"
    );
    if(is_array($msg))
        $error["msg"] = "$title [Error Code:$msg[0]] $msg[2]";
    $_500 = replace(ORIGIN.'template/500.html');
    include("$_500");
    errorLog($msg["msg"]);
    errorLog("in:{$msg["file"]}");
    errorLog("line:{$msg["line"]}");
    if($msg) unset($msg);
    if($title) unset($title);
    if($file) unset($file);
    if($error) unset($error);
    exit(0);
}

/**
 * @access public
 * @param array $error_arr 异常信息数组
 * @return void
 * @context 底层异常显示模块
 */
function base(array $error_arr)
{
    $error_msg = $error_arr["message"];
    $error_msg = explode("#",$error_msg);
    $error_zero = explode(" in ",$error_msg[0]);
    $error_zero[1] = explode(":",str_replace(ROOT,null,$error_zero[1]));
    $error_zero_line = intval($error_zero[1][1]);
    array_push($error_zero,"Line : ". $error_zero_line);
    array_push($error_zero, trim(str_replace($error_zero_line,null,$error_zero[1][1]))." :");
    $error_zero[1] = $error_zero[1][0];
    $error_zero[1] = "In : ".$error_zero[1];
    array_splice($error_msg,0,1,$error_zero);
    $_501 = replace(ORIGIN.'template/501.html');
    include("$_501");
    if($error_msg) unset($error_msg);
    if($error_zero) unset($error_zero);
    if($error_zero_line) unset($error_zero_line);
    if($error_arr) unset($error_arr);
    exit(0);
}

# 设置异常捕捉回调函数
register_shutdown_function("danger");
/**
 * @access public
 * @return void
 * @context 危险异常捕捉函数
 */
function danger()
{
    $error = error_get_last();
    define("E_FATAL",  E_ERROR | E_USER_ERROR |  E_CORE_ERROR |
        E_COMPILE_ERROR | E_RECOVERABLE_ERROR| E_PARSE );
    if($error && ($error["type"]===($error["type"] & E_FATAL))) {
        if(DEBUG) base($error);
    }
}