<?php
/**
 * Socket服务端执行函数
 * @access public
 * @param string $connect_ip 链接地址
 * @param string $connect_port 链接接口
 * @param int $listen_len 监听数据长度
 * @param int $read_len 读取数据长度
 * @param boolean $continue 持续读取
 * @return mixed
 */
function service($connect_ip,$connect_port,$listen_len,$read_len,$continue=true)
{
    # 调用Socket类型
    $_socket = new \Origin\Kernel\Protocol\Socket();
    #
    $_socket->connect($connect_ip,intval($connect_port));
    return null;
}
/**
 * Socket客户端执行函数
 * @access public
 * @param string $connect_ip 链接地址
 * @param string $connect_port 链接接口
 * @param int $read_len 读取数据长度
 * @return mixed
 */
function client($connect_ip,$connect_port,$read_len)
{
    return null;
}