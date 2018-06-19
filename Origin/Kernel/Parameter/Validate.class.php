<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Parameter.Validate *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/01/06 14:30
 * update Time: 2017/01/09 11:01
 * chinese Context:
 * IoC 变量验证封装类，可以对预设结构或自定义结构进行验证
 * 界限值只支持大于和小于，最大和最小界限值相等且都大于0时，表示验证值进行等于验证
 * 当最小值和最大值都等于0或者为空时表示验证值不受长度限制
 */
namespace Origin\Kernel\Parameter;
/**
 * 参数验证基类，公共类
 */
class Validate
{
    /**
     * 全局变量 用户方法间值得传递
     * 传递验证值
     * @access private
     * @var mixed $_Variable
     */
    private $_Variable = null;
    /**
     * 全局变量 用户方法间值得传递
     * 传递验证类型
     * @access private
     * @var string $_Type
     */
    private $_Type = null;
    /**
     * 全局变量 用户方法间值得传递
     * 传递最小值域
     * @access private
     * @var int $_Min
     */
    private $_Min = 0;
    /**
     * 全局变量 用户方法间值得传递
     * 传递最大值域
     * @access private
     * @var int $_Max
     */
    private $_Max = 0;
    /**
     * 全局变量 用户方法间值得传递
     * 传递至是否进行空验证，并根据选择状态，来进行其他验证执行
     * @access private
     * @var boolean $_Null
     */
    private $_Null = true;
    /**
     * 构造函数 对验证值及参数条件进行装载
     * @access public
     * @param mixed $variable 参数值
     * @param string $type 参数类型
     * @param int $min_range 最小值域
     * @param int $max_range 最大值域
     * @param boolean $is_null 是否为空
     */
    function __construct($variable, $type='redefine', $min_range=0, $max_range=0, $is_null=false)
    {
        /**
         * 验证模型正则数组
         * @var array $_regular
         * 数组中包含15中基础验证正则表达式
         * telephone：固定电话
         * mobile：移动电话 仅支持国内所有运营商号段
         * ipv4：ip4地址
         * ipv6：ip6地址
         * host：域名地址 支持多级域名及带参域名
         * chinese：中文
         * english：英文
         * numeric：纯数字
         * cn_name：名字，可以同时支持中文英文混写，也可以用来支持名字中出现单字母的名字
         * en_name：名字，可以同时支持英文，也可以用来支持名字中出现单字母的名字
         * nickname：昵称 支持中文英文数字及部分特殊符号,在一些特定条件下可以重构部分验证结构，提高验证精度
         * username：用户名 仅支持英文数字及部分特殊符号,在一些特定条件下可以重构部分验证结构，提高验证精度
         * weak：弱密码 支持纯英文数字或作为链接结构的部分特殊符号
         * strong：健壮密码 必须同时存在大写，小写，数字或部分特殊符号
         * safety：强密码 必须同时存在大写，小写，数据及部分特殊符号
         * redefine：自定义结构
         */
        # 正则表达式数组变量
        $_regular = array(
            'telephone' => '/^([0]{1}\d{2,3})?([^0]\d){1}\d{2,10}$/',
            'mobile' => ' /^[1][3|4|5|7|8]{1}\d{9}$/',
            'email' => '/^([^\_\W]+[\.\-]*)+\@(([^\_\W]+[\.\-]*)+\.)+[^\_\W]{2,8}$/',
            'ipv4' => '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/',
            'ipv6' => '/^([0-9A-F]{4}|[0-9a-f]{4}|:{1,2}){1,8}$/',
            'host' => '/^((http|https):\/\/)?(www.)?([\w\-]+\.)+[a-zA-z]+(\/[\w\-\.][a-zA-Z]+)*(\?([^\W\_][\w])+[\w\*\&\@\%\-=])?$/',
            'chinese' => '/^[\x{4e00}-\x{9fa5}]+$/u', # 中文检测需要在结尾夹u
            'english' => '/^[^\_\d\W]+$/',
            'numeric' => '/^\d+$/',
//            'cn_name' => '/^([u4e00-u9fafA-Za-z]+[\.\·\s]?)+$/u',
//            'cn_name' => '/^([\x7f-\xffA-Za-z]+[\.\·\s]?)+$/',
            'cn_name' => '/^[^\s\w\!\@\#\%\^\&\*\(\)\-\+\=\/\'\"\$\:\;\,\.\<\>\`\~]+$/',
            'en_name' =>  '/^([A-Za-z]+[\.\s]?)+$/',
            'nickname' => '/^[\u4e00-\u9faf\w\.\-\@\+\$\#\~\%\^\&]+$/',
            'username' => '/^[\w\.\-\@\+\$\#\*\~\%\^\&]+$/',
            'weak' => '/^([^\_\W]+([\_\.\-\@\+\$\#\*\~\%\^\&]*))+$/',
            'strong' => '/^([A-Z]+[a-z]+[0-9]+[\.\_\-\@\+\$\#\*\~\%\^\&]*)+$/',
            'safety' => '/^([A-Z]+[a-z]+[0-9]+[\.\_\-\@\+\$\#\*\~\%\^\&]+)+$/',
        );
        $this->_Variable = strval(trim($variable));
        if(array_key_exists(strval(trim($type)),$_regular)){
            $this->_Type = strval($_regular[trim($type)]);
        }else{
            $this->_Type = 'redefine';
        }
        $this->_Min = intval($min_range);
        $this->_Max = intval($max_range);
        $this->_Null = boolval($is_null);
    }
    /**
     * 重定义正则表达式接入方法
     * @access public
     * @param string $regular
     * @return null
    */
    public function regular($regular)
    {
        if($this->_Type == 'redefine') $this->_Type = $regular;
        return null;
    }
    /**
     * 执行验证功能默认方法
     * 本方法根据运算方法返回结构，进行基本判断，返回 pass 或者错误信息
     * @access public
     * @return mixed
    */
    public function main()
    {
        /**
         * @var mixed $_receipt
         * @var string $_size
         * @var string $_type
         * @var string $_empty
         * @var string $_ip
        */
        $_receipt = true;
        $_size = $this->__size();
        $_type = $this->__type();
        $_empty = $this->__empty();
        if($_empty !== true){
            $_receipt = $_empty;
        }elseif($_size !== true){
            $_receipt = $_size;
        }elseif($_type !== true){
            $_receipt = $_type;
        }else{
            if($this->_Type == 'ipv4'){
                $_ip = $this->__ipv4();
                if($_ip !== true){
                    $_receipt = $_ip;
                }
            }elseif($this->_Type == 'ipv6'){
                $_ip = $this->__ipv6();
                if($_ip !== true){
                    $_receipt = $_ip;
                }
            }
        }
        return $_receipt;
    }
    /**
     * 执行空值验证，当is_null为true时，验证将被跳过，反之则进行空值验证
     * 当验证通过函数返回complete，反之返回错误信息
     * @access private
     * @return string
    */
    private function __empty()
    {
        /**
         * @var mixed $_return
        */
        $_return = true;
        # 判断验证参数值是否被设置为可以为空
        if($this->_Null == false){
            # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
            if(is_array($this->_Variable)){
                # Origin Class Error: Unable to verify the array
                $_return = 'array object';
            }else{
                # 使用empty函数判断参数值是否为空并是用字符串长度判断函数，判断参数值长度，进行双重验证
                if(empty(trim($this->_Variable)) or strlen(trim($this->_Variable)) == 0){
                    # 由于empty函数特性，设置例外参数数据类型的验证，保证验证精度，由于当前版本值支持字符串验证，所以本结构段只有少量结构代码会被执行
                    if(is_int($this->_Variable) and $this->_Variable == 0)
                        $_return = 'complete';
                    elseif((is_float($this->_Variable) or is_double($this->_Variable))and $this->_Variable == 0.0)
                        $_return = 'complete';
                    elseif(is_bool($this->_Variable) and $this->_Variable == false)
                        $_return = 'complete';
                    elseif(is_string($this->_Variable) and $this->_Variable == '0')
                        $_return = 'complete';
                    else
                        # error: Verify the value is null
                        $_return = 'value null';
                }
            }
        }
        return $_return;
    }
    /**
     * 执行值长度范围验证，当is_null为true时，验证会被跳过，反之则进行验证
     * 如果最小范围值大于最大范围值，验证参数数值对调
     * 如果最小范围值等于最大范围值，值进行大于等于值的验证
     * 如果最小范围值大于0，最大范围值小于等于0，值进行大于等于值的验证
     * 如果最小范围值等于小于0，最大范围值大于等于0，值进行小于等于值的验证、
     * 如果最小范围值和最小范围值小于等于0，则不限制大小，参数值全部默认等于0
     * @access private
     * @return string
    */
    private function __size()
    {
        /**
         * @var mixed $_return
        */
        $_return = true;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        if(is_array($this->_Variable)){
            # Origin Class Error: Unable to verify the array
            $_return = 'array object';
        }else{
            # 判断验证参数值是否被设置为可以为空
            if($this->_Null == false or !empty(trim($this->_Variable)) or strlen(trim($this->_Variable)) > 0) {
                # 判断范围值是否都为小于0的参数，如果小于0，则参数值都等于0
                if ($this->_Min < 0) {
                    $this->_Min = 0;
                }
                if ($this->_Max < 0) {
                    $this->_Max = 0;
                }
                # 比对范围值大小，如果最小范围值大于最大范围值，执行参数值置换
                if ($this->_Min > $this->_Max and $this->_Max > 0) {
                    $_middle = $this->_Min;
                    $this->_Min = $this->_Max;
                    $this->_Max = $_middle;
                }
                # 当最大范围值和最小范围值相等时
                if(($this->_Min == $this->_Max) and $this->_Min > 0){
                    if(strlen($this->_Variable) != $this->_Min){
                        # Origin Class Error: Verify the value length do not agree with requirements
                        $_return = 'length error';
                    }
                }
                # 判断最小范围值大于零，且最大范围值等于0，执行大于等于值的判断
                # 如果最小范围值等于最大范围值，且值大于0，也将执行大于等于值的判断
                if (($this->_Min > 0 and $this->_Max == 0)) {
                    if (strlen($this->_Variable) < $this->_Min or mb_strlen($this->_Variable) < $this->_Min) {
                        # Origin Class Error: Verify the value length is less than the minimum range
                        $_return = 'length error';
                    }
                }
                # 判断最大范围值大于0，且最小范围之等于0，执行小等于值的判断
                if ($this->_Max > 0 and $this->_Min == 0) {
                    if (strlen($this->_Variable) > $this->_Max or mb_strlen($this->_Variable) > $this->_Max) {
                        # Origin Class Error: Verify the length value is greater than the maximum range
                        $_return = 'length error';
                    }
                }
                # 判断区间范围
                if($this->_Min > 0 and $this->_Max > 0 and $this->_Min < $this->_Max){
                    if(strlen($this->_Variable) < $this->_Min or mb_strlen($this->_Variable) < $this->_Min
                        or strlen($this->_Variable) > $this->_Max or mb_strlen($this->_Variable) > $this->_Max){
                        # Origin Class Error: Verify the value is beyond the range
                        $_return = 'length error';
                    }
                }
            }
            # 如果最小范围值和最大范围值都等于0，则不作任何判断
        }
        return $_return;
    }
    /**
     * 执行正则比对验证，当is_null为true时，且参数值为空，则不进行验证，反之进行验证
     * @access private
     * @return string
    */
    private function __type()
    {
        /**
         * @var mixed $_return
         */
        $_return = true;
        if($this->_Type == 'redefine'){
            # Origin Class Error: Your choice of validation structure does not exist
            $_return = 'type error';
        }else{
            # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
            # Origin Class Error: Unable to verify the array
            if(is_array($this->_Variable)){
                $_return = 'array object';
            }else{
                # 判断验证参数值是否被设置为可以为空
                if($this->_Null == false or !empty(trim($this->_Variable)) or strlen(trim($this->_Variable)) > 0){
                    if(!preg_match($this->_Type, $this->_Variable)){
                        $_return = 'type error';
                    }
                }
            }
        }

        return $_return;
    }

    /**
     * 执行ipv4地址验证，当is_null为true时，且参数值为空，则不进行验证，反之进行验证
     * @access private
     * @return string
    */
    private function __ipv4()
    {
        /**
         * @var mixed $_return
         * @var array _array
         * @var int i
         */
        $_return = true;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        # error: Verify the value format does not accord with requirements
        if(is_array($this->_Variable)){
            $_return = 'format error';
        }else {
            if($this->_Null == false or !empty(trim($this->_Variable)) or strlen(trim($this->_Variable)) > 0){
                # 拆分ip地址，将字符串转化为数组结构
                $_array = explode('.', $this->_Variable);
                # 判断转化后数组状态，是否可以进行进一步验证
                if (is_array($_array) and count($_array) == 4) {
                    # 循环遍历数组元素，进行ip地址信息特性验证
                    for ($i = 0; $i < count($_array); $i++) {
                        if ($i < 0 or $i > 255) {
                            $_return = 'format error';
                            break;
                        }
                    }
                } else {
                    $_return = 'format error';
                }
            }
        }
        return $_return;
    }
    /**
     * 执行ipv6地址验证，当is_null为true时，且参数值为空，则不进行验证，反之进行验证
     * @access private
     * @return string
     */
    private function __ipv6()
    {
        /**
         * @var mixed $_return
         * @var array _array
         * @var int i
         * @var int k
         * @var string $_keys
         * @var string $_regular
         */
        $_return = true;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        if(is_array($this->_Variable)){
            $_return = 'error: Verify the value format does not accord with requirements';
        }else {
            if($this->_Null == false or !empty(trim($this->_Variable)) or strlen(trim($this->_Variable)) > 0){
                # 判断ip地址是否为初始地址，即：0:0:0:0:0:0:0:0 或者 ::
                if($this->_Variable != '0:0:0:0:0:0:0:0' and $this->_Variable != '::'){
                    # 拆分ip地址，将字符串转化为数组结构
                    $_array = explode(':', $this->_Variable);
                    # 判断转化后数组状态，是否可以进行进一步验证
                    if (is_array($_array) and count($_array) >2 and count($_array) < 9) {
                        $k = 0;
                        $_keys = null;
                        # 循环遍历数组元素，进行ip地址信息特性验证
                        for ($i = 0; $i < count($_array); $i++) {
                            $_regular = '/^([0-9A-F]{1,4}|[0-9a-f]|)$/';
                            if (!preg_match($_regular, $_array[$i])) {
                                # error: Verify the value format does not accord with requirements
                                $_return = 'format error';
                                break;
                            }else{
                                # 查找ipv6数组中有多少个空元素
                                if($_array[$i] == ''){
                                    # 当存在空元素时，将其键存入预设变量中，并进行累加
                                    if($k == 0)
                                        $_keys .= $i;
                                    else
                                        $_keys .= ','.$i;
                                    $k += 1;
                                    # 当空元素值大于2个时，根据ipv6语法特性进行，结构运算
                                    if($k >2){
                                        $_keys = explode(',',$_keys);
                                        if(intval($_keys[0]) + 1 == intval($_keys[1]) and intval($_keys[0]) + 2 == intval($_keys[2])){
                                            $_return = 'format error';
                                        }
                                    }
                                }else{
                                    continue;
                                }
                            }
                        }
                    } else {
                        $_return = 'format error';
                    }
                }
            }
        }
        return $_return;
    }
}