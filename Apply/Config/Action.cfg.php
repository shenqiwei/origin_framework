<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/11
 * Time: 10:31
 */
# 数据库对象映射模板
return array(
    "user"=>array( # 对象控制器名称
        "listing" => array( # 对象方法名
            "data_source" => null,
            "action_type" => "select",
            "model_object" => "user",
        ),
    ),
    "test"=>array(
        "index" => array(
            "data_source" => "mysql_test",
            "action_type" => "insert",
            "model_object" => "user",
        ),
    )
);