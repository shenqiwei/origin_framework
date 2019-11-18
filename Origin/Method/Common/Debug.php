<?php
/**
 * @access public
 * @param string $obj 未加载对象（class|function）
 * @param string $error 错误信息
 * @param string $type 加载类型
 * @return null
 * @context 加载错误信息
 */
function notLoad($obj,$error,$type)
{
    include(str_replace('/',DS,ROOT.RING.'Template/Load.html'));
    return null;
}