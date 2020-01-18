<?php
/**
 * @context Application common configuration folder
 */
return array(
    # 数据库服务器配置(多数据库类型支持结构)
    'DATA_MATRIX_CONFIG' => array(
        array(
            "DATA_NAME" =>"origin", # 当前数据源名称
            "DATA_TYPE"=>"redis",
            "DATA_CONN" => "normal",# 连接类型 redis下设置生效
            'DATA_HOST' => 'localhost', # redis服务访问地址
            'DATA_PWD' => '', # redis登录密码
            'DATA_PORT' => '6379', # redis默认访问端口
            "DATA_P_CONNECT" => false, # 是否使用持久链接
        ),
    ),
);
