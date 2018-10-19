<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Apply[.Home].Config.Config *
 * version: 1.0 *
 * structure: common framework *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context:
 * create Time: 2017/01/01 9:35
 * update Time: 2017/01/12 10:45
 * IoC 框架配置文件
 */
return array(
    # 数据库服务器配置(多地址结构)
    'DATA_MATRIX_CONFIG' => array(
        array(
            "DATA_NAME" =>"mysql_test", # 当前数据源名称
            "DATA_TYPE"=>"mysql",# 连接类型 redis下设置生效
            'DATA_HOST' => 'localhost', # mysql服务访问地址
            'DATA_USER' => 'root', # mysql登录用户
            'DATA_PWD' => '', # mysql登录密码
            'DATA_PORT' => '3306', # mysql默认访问端口
            'DATA_DB' => 'zero_pro', # mysql访问数据库
        ),
    ),
);
