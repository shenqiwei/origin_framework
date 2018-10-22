<?php
/**
 * @access public
 * @param string $model 模板映射对象名称
 * @param string $obj 模板映射对象名称
 * @return mixed
 * @context 数据映射结构
 */
function Model($model,$obj=null)
{
    # 初始返回值变量
    $_receipt = null;
    # 判断调取映射对象状态
    # 调取注册表信息
    if(indexFiles($_url = Config("ROOT_APPLICATION").Config("APPLICATION_CONFIG").'Model/'.$model.".model.php")){
        $_register = include(str_replace("/",SLASH,$_url));
    }else{
        # 异常提示：行为配置文件无效
        try{
            throw new \Exception('Origin Action Model loading Error: Config is invalid for Model config');
        }catch(\Exception $e){
            echo($e->getMessage());
            exit();
        }
    }
    # 装载配置信息
    $_register = array_change_key_case($_register,CASE_LOWER);
    # 装载注册信息
    if(key_exists($_obj = trim(strtolower($obj)),$_register)){
        # 装载配置信息
        $_receipt = array_change_key_case($_register[$_obj],CASE_LOWER);
    }else{
        # 判断调取映射执行内容是否已经注册
        if(key_exists($_obj = trim(strtolower($model)),$_register)){
            # 装载配置信息
            $_receipt = array_change_key_case($_register[$_obj],CASE_LOWER);
        }else{
            if(count($_register)){
                # 装载配置信息
                $_receipt = array_change_key_case($_register[array_keys($_register)[0]],CASE_LOWER);
            }
        }
    }
    # 返回对象映射
    return $_receipt;
}
/**
 * @access public
 * @param string $model 模板映射文件名称
 * @param string $obj 模板映射对象名称
 * @return mixed
 * @context 行为映射结构
 */
function Action($model,$obj=null){
    # 初始返回值变量
    $_receipt = null;
    # 判断调取映射对象状态
    # 调取注册表信息
    if(indexFiles($_url = Config("ROOT_APPLICATION").Config("APPLICATION_CONFIG").'Action/'.$model.".model.php")){
        $_register = include(str_replace("/",SLASH,$_url));
    }else{
        # 异常提示：行为配置文件无效
        try{
            throw new \Exception('Origin Action Config Error: Config is invalid for model config');
        }catch(\Exception $e){
            echo($e->getMessage());
            exit();
        }
    }
    # 键值转化
    $_register = array_change_key_case($_register,CASE_LOWER);
    # 装载注册信息
    if(key_exists($_obj = trim(strtolower($obj)),$_register)){
        # 装载配置信息
        $_receipt = array_change_key_case($_register[$_obj],CASE_LOWER);
    }else{
        # 判断调取映射执行内容是否已经注册
        if(key_exists($_obj = trim(strtolower($model)),$_register)){
            # 装载配置信息
            $_receipt = array_change_key_case($_register[$_obj],CASE_LOWER);
        }else{
            if(count($_register)){
                # 装载配置信息
                $_receipt = array_change_key_case($_register[array_keys($_register)[0]],CASE_LOWER);
            }
        }
    }
    # 返回对象映射
    return $_receipt;
}