<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.3
 * @copyright 2015-2017
 * @context: IoC 标签二维解析器
 * 根据二维解释器程序结构特性，解释器会将一维结构中所有的应用逻辑进行数组降维展开，
 * 所以当数据维度超过一维结构时结构解释将返回null字节，同结构标签将无法完成维度解析
 * 该结构设计限制值针对企业定制框架模型及开源社区框架结构
 */
namespace Origin\Kernel\Graph;
/**
 * 标签解析主函数类
 */
class Label
{
    /**
     * 变量标记标签规则
     * @var string $_Var_Regular
    */
    private $_Variable = '/{\$[^_\W\s]+([_-]?[^_\W]+)*(\.\[\d+]|\.[^_\W\s]+([_-]?[^_\W]+)*)*(\|[^_\W\s]+([_-]?[^_\W]+)*)?}/';
    /**
     * 页面引入标签规则
     * @var string $_Include_Regular <include href="src/html/page.html"/>
     */
    private $_Include_Regular = '/\<include\s+href\s*=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?>/';
    /**
     * 逻辑判断标记规则
     * @var string $_Judge_Ci condition_information : 'variable eq conditions_variable'
     * @var string $_Judge_Si Symbol
     * @var string $_Judge_If if <if condition = 'variable eq conditions_variable'>
     * @var string $_Judge_EF elseif <elseif condition = 'variable eq conditions_variable'/>
     * @var string $_Judge_El else <else/>
     * @var string $_Judge_El end </if>
     */
//    private $_Judge_Si ='/\s(eq|gt|ge|lt|le|neq|heq|nheq|in)\s/';
    private $_Judge_If = '/\<if\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $_Judge_EF = '/\<elseif\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?\>/';
    private $_Judge_El = '/\<else[\/]?\>/';
    private $_Judge_Ie = '/\<[\/]if\s*\>/';
    /**
     * 循环执行标签规则
     * @var string $_For_Operation 'variable to circulation_count'
     * @var string $_For_Begin <for operation = 'variable to circulation_count'>
     * @var string $_For_End </for>
     */
//    private $_For_Operate = '/^.+(\s(to)\s.+(\s(by)\s.+)?)?$/';
    private $_For_Begin = '/\<for\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $_For_End = '/<\/for\s*>/';
    /**
     * foreach循环标签规则
     * @var string $_Foreach_Operation 'variable (as mark_variable)'
     * @var string $_Foreach_Begin <foreach operation = '(as mark_variable)'>
     * @var string $_Foreach_End </foreach>
     */
//    private $_Foreach_Operate= '/^.+\s(as)\s.+$/';
    private $_Foreach_Begin = '/\<foreach\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $_Foreach_End = '/\<[\/]foreach\s*\>/';
    /**
     * 解析代码
     * @var string $_Obj
    */
    private $_Obj;
    /**
     * 构造方法 获取引用页面地址信息
     * @access public
     * @param string $page
     * @param array $param
     */
    function __construct($page)
    {
        $this->_Obj = $page;
    }
    /**
     * 默认函数，用于对模板中标签进行转化和结构重组
     * @access public
     * @return string
    */
    function execute()
    {
        # 创建初始标签标记变量\
        $_initialize = null;
        $_obj = file_get_contents($this->_Obj);
        # 去标签差异化
        $_obj = preg_replace('/\s*\\\:\s*(end)\s*\\\>/', ':end>',$_obj);
        # 转义引入结构
        $_obj = $this->__include($_obj);
        $_obj = $this->__for($_obj);
        $_obj = $this->__foreach($_obj);
        $_obj = $this->__if($_obj);
        $_obj = $this->variable($_obj);
        # 去除空白注释
        $_obj = preg_replace('/\\\<\\\!\\\-\\\-\s*\\\-\\\-\\\>/',"\r\n",str_replace('<!---->','',$_obj));
        # 去多余标签结构
        $_obj = preg_replace($this->_For_End, '', $_obj);
        $_obj = preg_replace($this->_Foreach_End, '', $_obj);
        $_obj = preg_replace($this->_Judge_EF, '', $_obj);
        $_obj = preg_replace($this->_Judge_El, '', $_obj);
        $_obj = preg_replace($this->_Judge_Ie, '', $_obj);
        # 遍历资源目录，替换资源信息
        $_obj = str_replace('__RESOURCE__',__HOST__.__RESOURCE__, $_obj);
        if(MARK_RELOAD){
            $_obj = preg_replace('/\s+/', ' ', $_obj);
        }
        return $_obj;
    }
    /**
     * 引入结构标签解释方法
     * @access protected
     * @param string $obj
     * @return string
     */
    function __include($obj)
    {
        # 获取include标签信息
        $_count = preg_match_all($this->_Include_Regular, $obj, $_include, PREG_SET_ORDER);
        # 遍历include对象内容
        for($_i = 0;$_i < $_count; $_i++){
            # 拼接引入文件地址信息
            $_files = __RESOURCE__.'/Public/'.str_replace('"','',$_include[$_i][1]);
            # 判断文件完整度
            if(is_file($_files)){
                # 读取引入对象内容
                $_mark = file_get_contents(ROOT.str_replace('/',DS,$_files));
                # 执行结构内容替换
                $obj = str_replace($_include[$_i][0],$_mark,$obj);
            }
        }
        return $obj;
    }
    /**
     * 变量标签解释方法
     * @access protected
     * @param string $obj
     * @return string
    */
    function variable($obj)
    {
        # 传入参数为初始化状态，对代码段进行筛选过滤
        preg_match_all($this->_Variable, $obj, $_label, PREG_SET_ORDER);
        # 迭代标记信息
        for($i=0; $i<count($_label);$i++) {
            # 存在连接符号,拆分标记
            $_var = str_replace('}', '', str_replace('{', '', $_label[$i][0]));
            # 判断是否包含函数调用
            if(strpos($_var,"|")){
                $_var = explode("|",$_var);
                $_method = $_var[1];
                $_var = $_var[0];
            }
            # 拆分变量
            if(strpos($_var,".")){
                $_var = explode(".",$_var);
                $_variable = null;
                for($_i = 0;$_i < count($_var);$_i++){
                    if(empty($_i))
                        $_variable = $_var[$_i];
                    else{
                        if(preg_match("/^\[.+]$/",$_var[$_i]))
                            $_variable .= $_var[$_i];
                        else
                            $_variable .= "[\"{$_var[$_i]}\"]";
                    }
                }
                $_var = $_variable;
            }
            # 验证函数是否已创建
            if(isset($_method) and  function_exists($_method))
                $obj = str_replace($_label[$i][0],"<?php echo({$_method}({$_var})); ?>",$obj);
            else
                $obj = str_replace($_label[$i][0],"<?php echo({$_var}); ?>",$obj);
        }
        return $obj;
    }
    /**
     * 逻辑判断标签解释方法
     * @access protected
     * @param string $obj
     * @param boolean $dr
     * @return string
    */
    function __if($obj, $dr=true)
    {
        # 获取if标签
        $_count = preg_match_all($this->_Judge_If,$obj , $_IF, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            # 获取条件内容
            $_condition =  preg_replace('/[\'\"]*/', '', $_IF[$_i][1]);
            # 拆分条件内容
            $_condition = explode("\s",$_condition);
            $_symbol = $this->symbol($_condition[1]);
            if(is_numeric($_condition[2])){
                if(strpos($_condition[2],"."))
                    $_comparison = floatval($_condition[2]);
                else
                    $_comparison = intval($_condition[2]);
            }else
                $_comparison = strval($_condition[2]);
            $obj = str_replace($_IF[$_i][0],"<?php if({$_condition} {$_symbol} {$_comparison}){?>",$obj);
        }
        # 获取elseif标签
        $_count = preg_match_all($this->_Judge_EF, $obj, $_EF, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            # 获取条件内容
            $_condition =  preg_replace('/[\'\"]*/', '', $_EF[$_i][1]);
            # 拆分条件内容
            $_condition = explode("\s",$_condition);
            $_symbol = $this->symbol($_condition[1]);
            if(is_numeric($_condition[2])){
                if(strpos($_condition[2],"."))
                    $_comparison = floatval($_condition[2]);
                else
                    $_comparison = intval($_condition[2]);
            }else
                $_comparison = strval($_condition[2]);
            $obj = str_replace($_EF[$_i][0],"<?php }elseif({$_condition} {$_symbol} {$_comparison}){?>",$obj);
        }
        # 转义else逻辑语法
        if(preg_match($this->_Judge_El, $obj, $_ELSE, PREG_SET_ORDER))
            $obj = str_replace($_ELSE[0][0], "<?php }else{ ?>", $obj);
        # 转义if逻辑结尾标签
        if(preg_match($this->_Judge_EF, $obj, $_EIF, PREG_SET_ORDER))
            $obj = str_replace($_EIF[0][0],"<?php } ?>",$obj);
        return $obj;
    }
    /**
     * 逻辑批处理方法
     * @access protected
     * @param string $symbol
     * @return string;
    */
    function symbol($symbol)
    {
        $_symbol = array(
            "gt" => ">","lt" => "<","ge" => ">=","le" => "<=",
            "heq" => "===","nheq" => "!==","eq" => "==","neq" => "!="
        );
        if(key_exists($symbol,$_symbol))
            return $_symbol[$symbol];
        else
            return "==";

    }
    /**
     * for标签结构解释方法
     * @access public
     * @param string $obj 进行解析的代码段
     * @return string
     */
    function __for($obj)
    {
        # 获取当前代码段中是否存在foreach标签
        $_count = preg_match_all($this->_For_Begin, $obj, $_begin, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            $_operate = preg_replace('/[\'\"]*/', '', $_begin[$_i][1]);
            $_operate_i = "\$i_".str_replace("\$",null,$_operate);
            $obj = str_replace($_begin[$_i][0],"<?php for({$_operate_i}=0;$_operate_i < count({$_operate});{$_operate_i}++){ ?>",$obj);
            # 传入参数为初始化状态，对代码段进行筛选过滤
            preg_match_all($this->_Variable, $obj, $_label, PREG_SET_ORDER);
            # 迭代标记信息
            for($i=0; $i<count($_label);$i++) {
                # 存在连接符号,拆分标记
                $_var = str_replace('}', '', str_replace('{', '', $_label[$i][0]));
                # 验证拆分方法
                if(strpos($_var,"|")){
                    $_var = explode("|",$_var);
                    $_function = $_var[1];
                    $_var = $_var[0];
                }
                # 拆分变量
                if (strpos($_var, ".")) {
                    $_var = explode(".", $_var);
                    if($_var[0] == $_operate){
                        $_variable = null;
                        for($_m = 0;$_m < count($_var);$_m++){
                            if(empty($_m)){
                                $_variable = "$_var[$_m][{$_operate_i}]";
                            }else{
                                $_variable .= "[\"{$_var[$_m]}\"]";
                            }
                        }
                        if(isset($_function))
                            $obj = str_replace($_label[$i][0],"<?php echo({$_function}({$_variable})); ?>",$obj);
                        else
                            $obj = str_replace($_label[$i][0],"<?php echo({$_variable}); ?>",$obj);
                    }
                }
            }
        }
        # 转义foreach逻辑结尾标签
        if(preg_match_all($this->_For_End, $obj, $_end, PREG_SET_ORDER))
            $obj = str_replace($_end[0][0],"<?php } ?>",$obj);
        return $obj;
    }
    /**
     * foreach循环标签解释方法
     * @access protected
     * @param string $obj
     * @return string
    */
    function __foreach($obj)
    {
        $_count = preg_match_all($this->_Foreach_Begin, $obj, $_begin, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            $_operate = preg_replace('/[\'\"]*/', '', $_begin[$_i][1]);
            $_operate = explode(" as ",$_operate);
            $_as_name = $_operate[1];
            $_operate = $_operate[0];
            $obj = str_replace($_begin[$_i][0],"<?php foreach({$_operate} as \${$_as_name}){ ?>",$obj);
            # 传入参数为初始化状态，对代码段进行筛选过滤
            preg_match_all($this->_Variable, $obj, $_label, PREG_SET_ORDER);
            # 迭代标记信息
            for($i=0; $i<count($_label);$i++) {
                # 存在连接符号,拆分标记
                $_var = str_replace('}', '', str_replace('{', '', $_label[$i][0]));
                # 验证拆分方法
                if(strpos($_var,"|")){
                    $_var = explode("|",$_var);
                    $_function = $_var[1];
                    $_var = $_var[0];
                }
                # 拆分变量
                if (strpos($_var, ".")) {
                    $_var = explode(".", $_var);
                    if($_var[0] == $_operate) {
                        $_variable = null;
                        for ($_m = 0; $_m < count($_var); $_m++) {
                            if (empty($_m)) {
                                $_variable = "$_var[$_m]";
                            } else {
                                $_variable .= "[\"{$_var[$_m]}\"]";
                            }
                        }
                        if(isset($_function))
                            $obj = str_replace($_label[$i][0],"<?php echo({$_function}({$_variable})); ?>",$obj);
                        else
                            $obj = str_replace($_label[$i][0],"<?php echo({$_variable}); ?>",$obj);
                    }
                }
            }
        }
        # 转义foreach逻辑结尾标签
        if(preg_match($this->_Foreach_End, $obj, $_end, PREG_SET_ORDER))
            $obj = str_replace($_end[0][0],"<?php } ?>",$obj);
        return $obj;
    }
}