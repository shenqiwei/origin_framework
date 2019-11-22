<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2017
 */
/**
 * 参数信息强制转化函数方法
 * @access public
 * @param string $value
 * @param string $type
 * @param string $default
 * @return mixed
*/
function Filter($value, $type='string', $default=null){
    $_receipt = null;
    # 参数信息是否符合验证要求
    if($value !== null and preg_match('/^(string|int|integer|double|float|boolean|)$/', strtolower($type))){
        $_filter = new Origin\Kernel\Parameter\Filter($value, $type, $default);
        $_receipt =$_filter->main();
    }else{
        # 异常提示：参数值无效
        try{
            throw new Exception('The parameter value is invalid');
        }catch(Exception $e){
            $_output = new Origin\Kernel\Parameter\Output();
            $_output->exception("Filter Error",$e->getMessage(),debug_backtrace(0,1));
            exit();
        }
    }
    return $_receipt;
}
