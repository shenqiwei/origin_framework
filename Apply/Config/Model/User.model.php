<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/9/26
 * Time: 11:13
 */
# 接口请求结构模板
return array(
    "User" => array(
        "method" => "post",
        "model" => array(
            array("name" => "user_name","type"=>"string","is_to"=>false,"to_valid" => true),
            array("name"=>"user_age","type"=>"int","is_to"=>true,"to_valid" =>false),
            array("name"=>"user_sex","type"=>"boolean","is_to"=>true),
            array("name"=>"user_address","type"=>"string","is_to"=>false,"to_valid" => true),
        ),
        "valid" => array(
            "user_name" => array("max"=>"8","min"=>"3","not_null"=>false),
            "user_address" => array("max"=>"8","min"=>"3","format_string"=>"/^.*$/"),
        )
    ),
);