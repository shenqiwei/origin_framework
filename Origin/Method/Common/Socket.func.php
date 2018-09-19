<?php
/**
 * Socket 调用函数
 * @access public
 * @return object
 */
function socket(){
    $_socket = new \Origin\Kernel\Protocol\Socket();
    $_socket->__setObj($_socket);
    return $_socket;
}