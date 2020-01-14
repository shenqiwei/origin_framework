<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2019
 */
function initialize()
{
    $_log = Config('ROOT_LOG').Config('LOG_INITIALIZE').'initialize.log';
    # 判断日志文件
    if(!is_file(str_replace("/",DS,ROOT.$_log))){
        $_date = date("Y-m-d");
        # 调用日志
        note($_log,"Origin framework initialization on {$_date} ");
        # 创建初始化列表
        $_ini = array(
            "catalog" => array( # 根目录
                ROOT."Application", # 应用目录
                ROOT."Application/Common", # 应用公共函数目录
                ROOT."Application/Config", # 应用公共配置目录
                ROOT."Application/".Config("DEFAULT_APPLICATION"), # 默认应用主目录
                ROOT."Application/".Config("DEFAULT_APPLICATION")."/Common", # 默认应用公共函数目录
                ROOT."Application/".Config("DEFAULT_APPLICATION")."/Controller", # 默认应用控制器目录
                ROOT."Application/".Config("DEFAULT_APPLICATION")."/View", # 默认应用模板目录
                ROOT."Application/".Config("DEFAULT_APPLICATION")."/View/Index", # 默认应用模板目录
                ROOT.Config("ROOT_RESOURCE")."/Public", # 公共文件目录
                ROOT.Config("ROOT_RESOURCE")."/Public/Temp", # 500,404自定义模板位置
                ROOT.Config("ROOT_RESOURCE")."/Upload", # 上传文件目录
                ROOT.Config("ROOT_RESOURCE")."/Buffer", # 缓存文件目录
            ),
            "folder" => array(
                ROOT."Application" => array(
                    "Common/Public.php",
                    "Config/Config.php",
                    "Config/Route.php",
                    Config("DEFAULT_APPLICATION")."/Common/Public.php",
                    Config("DEFAULT_APPLICATION")."/Controller/Index.php",
                    Config("DEFAULT_APPLICATION")."/View/Index/index.html",
                ),
            )
        );
        note($_log,"Origin initialize ...");
        # 遍历配置数组
        foreach($_ini as $_key => $_array){
            # 配置信息为主目录
            if(strtolower($_key) == "catalog"){
                # 遍历数组内容
                for($_i = 0;$_i < count($_array);$_i++){
                    $_datetime = date("Y-m-d H:i:s",time());
                    # 判断文件目录是否创建
                    if(is_dir($_array[$_i])){
                        note($_log,"[{$_datetime}] directory：{$_array[$_i]}, created...");
                    }else{
                        # 创建目录
                        if(mkdir(str_replace("/",DS,$_array[$_i]),0777)){
                            note($_log,"[{$_datetime}] directory：{$_array[$_i]}, created...[complete]");
                        }else{
                            note($_log,"[{$_datetime}] directory：{$_array[$_i]}, created...[failed]");
                        }
                    }
                }
            }elseif(strtolower($_key) == "folder"){
                # 遍历二级配置目录
                foreach($_array as $_directory => $_dir){
                    # 遍历数组内容
                    for($_i = 0;$_i < count($_dir); $_i++) {
                        # 写入日志
                        $_datetime = date("Y-m-d H:i:s",time());
                        # 判断文件目录是否创建
                        if(is_file(ROOT."Application".str_replace("/",DS,"/{$_dir[$_i]}"))){
                            note($_log,"[{$_datetime}] file：{$_dir[$_i]}, created...");
                        }else{
                            # 拷贝应用预设文件
                            if(copy(ROOT.str_replace("/",DS,"Origin/lMethod/Storage/{$_dir[$_i]}"),ROOT."Application".str_replace("/",DS,"/{$_dir[$_i]}"))){
                                note($_log,"[{$_datetime}] file：{$_dir[$_i]}, copy...[complete]");
                            }else{
                                note($_log,"[{$_datetime}] file：{$_dir[$_i]}, copy...[failed]");
                            }
                            # 修改权限
                            if(chmod(ROOT."Application".str_replace("/",DS,"/{$_dir[$_i]}"),0777)){
                                note($_log,"[{$_datetime}] file：{$_dir[$_i]}, changed limit ...[complete]");
                            }else{
                                note($_log,"[{$_datetime}] file：{$_dir[$_i]}, changed limit...[failed]");
                            }
                        }
                    }
                }
            }
        }
        # 调用日志
        note($_log,"Initialization complete, thank you for use Origin framework ... :P");
        return true;
    }else{
        return false;
    }
}

function note($folder,$context)
{
    $_folder = explode("/",$folder);
    $_dir = null;
    for($_i = 0;$_i < count($_folder);$_i++){
        if($_i == count($_folder) - 1){
            break;
        }else{
            if(empty($_i))
                $_symbol = null;
            else
                $_symbol = DS;
            $_dir .= $_symbol.$_folder[$_i];
            if(!is_dir(ROOT.$_dir)){
                mkdir(ROOT.$_dir);
            }
        }
    }
    $_receipt = false;
    # 使用写入方式进行日志创建创建和写入
    $_handle = fopen(ROOT.DS.$folder,"a");
    if($_handle){
        # 执行写入操作，并返回操作回执
        $_receipt = fwrite($_handle,$context.PHP_EOL);
        # 关闭文件源
        fclose($_handle);
    }
    return $_receipt;
}