<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Function.Common.Filter *
 * version: 0.1*
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context:
 * IoC 拦截器函数包
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
        $_filter = new \Origin\Kernel\Parameter\Filter($value, $type, $default);
        $_receipt =$_filter->main();
    }else{
        # 异常提示：参数值无效
        try{
            throw new Exception('Origin Method Error: The parameter value is invalid');
        }catch(Exception $e){
            echo($e->getMessage());
            exit();
        }
    }
    return $_receipt;
}
