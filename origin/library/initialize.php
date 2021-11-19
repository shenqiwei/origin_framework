<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @return bool
 * @context 框架初始化函数
 */
function initialize(): bool
{
    $log = LOG_INITIALIZE.'initialize.log';
    # 判断日志文件
    if(!is_file(replace(ROOT.DS.$log))){
        $date = date("Y-m-d");
        # 调用日志
       _log($log,"Origin framework initialization on $date".PHP_EOL);
        # 创建初始化列表
        $ini = array(
            "catalog" => array( # 根目录
                ROOT."/application", # 应用目录
                ROOT."/application/common", # 应用公共函数目录
                ROOT."/common", # 公共文件目录
                ROOT."/common/config", # 配置文件目录
                ROOT."/common/log", # 日志文件目录
                ROOT."/application/".DEFAULT_APPLICATION, # 默认应用主目录
                ROOT."/application/".DEFAULT_APPLICATION."/common", # 默认应用公共函数目录
                ROOT."/application/".DEFAULT_APPLICATION."/classes", # 默认应用控制器目录
                ROOT."/application/".DEFAULT_APPLICATION."/template", # 默认应用模板目录
                ROOT."/application/".DEFAULT_APPLICATION."/template/index", # 默认应用模板目录
                ROOT_RESOURCE,
                RESOURCE_PUBLIC, # 公共文件目录
                RESOURCE_PUBLIC."/cache",
                RESOURCE_PUBLIC."/font",
                RESOURCE_PUBLIC."/queue",
                RESOURCE_PUBLIC."/template", # 500,404自定义模板位置
                RESOURCE_UPLOAD, # 上传文件目录
            ),
            "folder" => array(
                ROOT."/application" => array(
                    "common/public.php",
                    DEFAULT_APPLICATION."/common/public.php",
                    DEFAULT_APPLICATION."/classes/Index.php",
                    DEFAULT_APPLICATION."/template/index/index.html",
                ),
                ROOT."/common"=> array(
                    "config/config.php",
                    "config/route.php",
                ),
                RESOURCE_PUBLIC => array(
                    "font/origin001.ttf"
                ),
            )
        );
       _log($log,"Origin initialize ...".PHP_EOL);
        # 遍历配置数组
        foreach($ini as $key => $array){
            # 配置信息为主目录
            if(strtolower($key) == "catalog"){
                # 遍历数组内容
                for($i = 0;$i < count($array);$i++){
                    $datetime = date("Y-m-d H:i:s",time());
                    # 判断文件目录是否创建
                    if(is_dir($array[$i])){
                       _log($log,"[$datetime] directory：$array[$i], created...".PHP_EOL);
                    }else{
                        # 创建目录
                        if(mkdir(replace($array[$i]))){
                           _log($log,"[$datetime] directory：$array[$i], created...[complete]".PHP_EOL);
                        }else{
                           _log($log,"[$datetime] directory：$array[$i], created...[failed]".PHP_EOL);
                        }
                    }
                }
            }elseif(strtolower($key) == "folder"){
                # 遍历二级配置目录
                foreach($array as $directory => $dir){
                    # 遍历数组内容
                    for($i = 0;$i < count($dir); $i++) {
                        # 写入日志
                        $datetime = date("Y-m-d H:i:s",time());
                        # 判断文件目录是否创建
                        if(is_file($directory.replace("/$dir[$i]"))){
                           _log($log,"[$datetime] file：$dir[$i], created...".PHP_EOL);
                        }else{
                            # 拷贝应用预设文件
                            if(copy(ROOT.replace("/origin/library/storage/$dir[$i]"),$directory.replace("/$dir[$i]"))){
                               _log($log,"[$datetime] file：$dir[$i], copy...[complete]".PHP_EOL);
                            }else{
                               _log($log,"[$datetime] file：$dir[$i], copy...[failed]".PHP_EOL);
                            }
                            # 修改权限
                            if(chmod(ROOT."/application".replace("/$dir[$i]"),0777)){
                               _log($log,"[$datetime] file：$dir[$i], changed limit ...[complete]".PHP_EOL);
                            }else{
                               _log($log,"[$datetime] file：$dir[$i], changed limit...[failed]".PHP_EOL);
                            }
                        }
                    }
                }
            }
        }
        # 调用日志
       _log($log,"Initialization complete, thank you for use Origin framework ... :P".PHP_EOL);
        return true;
    }else{
        return false;
    }
}
