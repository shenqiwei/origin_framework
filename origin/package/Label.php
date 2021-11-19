<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架标签二维解析器
 * 根据二维解释器程序结构特性，解释器会将一维结构中所有的应用逻辑进行数组降维展开，
 * 所以当数据维度超过一维结构时结构解释将返回null字节，同结构标签将无法完成维度解析
 * 该结构设计限制值针对企业定制框架模型及开源社区框架结构
 */
namespace Origin\Package;
/**
 * 标签解析主函数类
 */
class Label
{
    /**
     * @access private
     * @var string $ViewCode 解析代码
     */
    private $ViewCode;

    /**
     * @access private
     * @var string $Variable 变量标记标签规则(输出)
     */
    private $Variable = '/{\$[^_\W\s]+([_-]?[^_\W\s]+)*(\.\[\d+]|\.[^_\W\s]+([_-]?[^_\W\s]+)*)*(\|[^_\W\s]+([_-]?[^_\W\s]+)*)?}/';

    /**
     * @access private
     * @var string $VariableI 变量标记标签规则
    */
    private $VariableI = '/\$[^_\W\s]+([_-]?[^_\W\s]+)*(\.\[\d+]|\.[^_\W\s]+([_-]?[^_\W\s]+)*)*(\|[^_\W\s]+([_-]?[^_\W\s]+)*)?/';

    /**
     * @access private
     * @var string $IncludeRegular <include href="src/html/page.html"/> 页面引入标签规则
     */
    private $Include = '/\<include\s+href\s*=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?>/';

    /**
     * 逻辑判断标记规则
     * @access private
     * @var string $JudgeCi condition_information : 'variable eq conditions_variable'
     * @var string $JudgeSi Symbol
     * @var string $JudgeIf if <if condition = 'variable eq conditions_variable'>
     * @var string $JudgeEF elseif <elseif condition = 'variable eq conditions_variable'/>
     * @var string $JudgeEl else <else/>
     * @var string $JudgeEl end </if>
     */
    private $JudgeIf = '/\<if\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $JudgeEF = '/\<elseif\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?\>/';
    private $JudgeEl = '/\<else[\/]?\>/';
    private $JudgeIe = '/\<[\/]if\s*\>/';

    /**
     * 循环执行标签规则
     * @access private
     * @var string $ForOperation 'variable to circulation_count'
     * @var string $ForBegin <for operation = 'variable to circulation_count'>
     * @var string $ForEnd </for>
     */
    private $ForBegin = '/\<for\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $ForEnd = '/<\/for\s*>/';

    /**
     * foreach循环标签规则
     * @access private
     * @var string $ForeachOperation 'variable (as mark_variable)'
     * @var string $ForeachBegin <foreach operation = 'variable (as mark_variable)'>
     * @var string $ForeachEnd </foreach>
     */
    private $ForeachBegin = '/\<foreach\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $ForeachEnd = '/\<[\/]foreach\s*\>/';

    /**
     * 构造方法 获取引用页面地址信息
     * @access public
     * @param string $page 视图模板内容信息
     * @return void
     */
    function __construct(string $page)
    {
        $this->ViewCode = $page;
    }

    /**
     * 默认函数，用于对模板中标签进行转化和结构重组
     * @access public
     * @return string
    */
    function execute(): string
    {
        # 创建初始标签标记变量
        $obj = file_get_contents($this->ViewCode);
        # 去标签差异化
        $obj = preg_replace('/\s*\\\:\s*(end)\s*\\\>/', ':end>',$obj);
        # 转义引入结构
        $obj = $this->__include($obj);
        $obj = $this->__for($obj);
        $obj = $this->__foreach($obj);
        $obj = $this->__if($obj);
        $obj = $this->variable($obj);
        # 去除空白注释
        $obj = preg_replace('/\\\<\\\!\\\-\\\-\s*\\\-\\\-\\\>/',"\r\n",str_replace('<!---->','',$obj));
        # 去多余标签结构
        $obj = preg_replace($this->ForEnd, '', $obj);
        $obj = preg_replace($this->ForeachEnd, '', $obj);
        $obj = preg_replace($this->JudgeEF, '', $obj);
        $obj = preg_replace($this->JudgeEl, '', $obj);
        $obj = preg_replace($this->JudgeIe, '', $obj);
        # 遍历资源目录，替换资源信息
        $obj = str_replace('__RESOURCE__',WEB_RESOURCE, $obj);
        if(MARK_RELOAD)
            $obj = preg_replace('/\s+/', ' ', $obj);
        return $obj;
    }

    /**
     * 引入结构标签解释方法
     * @access protected
     * @param string $obj 解析代码段
     * @return string
     */
    function __include(string $obj): string
    {
        # 获取include标签信息
        $count = preg_match_all($this->Include, $obj, $include, PREG_SET_ORDER);
        # 遍历include对象内容
        for($i = 0;$i < $count; $i++){
            # 拼接引入文件地址信息
            $files = DIR_RESOURCE.'/public/'.str_replace('"','',$include[$i][1]);
            # 判断文件完整度
            if(is_file($files)){
                # 读取引入对象内容
                $mark = file_get_contents(ROOT.DS.replace($files));
                # 执行结构内容替换
                $obj = str_replace($include[$i][0],$mark,$obj);
            }
        }
        return $obj;
    }

    /**
     * 变量标签解释方法
     * @access protected
     * @param string $obj 解析代码段
     * @return string
    */
    function variable(string $obj): string
    {
        # 传入参数为初始化状态，对代码段进行筛选过滤
        preg_match_all($this->Variable, $obj, $label, PREG_SET_ORDER);
        # 迭代标记信息
        for($i=0; $i<count($label);$i++) {
            # 存在连接符号,拆分标记
            $var = str_replace('}', '', str_replace('{', '', $label[$i][0]));
            # 拆分变量
            if(strpos($var,".")){
                $var = explode(".",$var);
                $variable = null;
                for($i = 0;$i < count($var);$i++){
                    if(empty($i))
                        $variable = $var[$i];
                    elseif($i == count($var)-1){
                        # 验证拆分方法
                        if(strpos($var[$i],"|")){
                            $vars = explode("|",$var[$i]);
                            $function = $vars[1];
                            $variable .= "[\"$vars[0]\"]";
                            $obj = str_replace($label[$i][0],"<?php echo($function($variable)); ?>",$obj);
                        }else{
                            $variable .= "[\"$var[$i]\"]";
                            $obj = str_replace($label[$i][0],"<?php echo($variable); ?>",$obj);
                        }
                    }else{
                        if(preg_match("/^\[.+]$/",$var[$i]))
                            $variable .= $var[$i];
                        else
                            $variable .= "[\"$var[$i]\"]";
                    }
                }
            }else{
                # 验证拆分方法
                if(strpos($var,"|")){
                    $var = explode("|",$var);
                    $obj = str_replace($label[$i][0],"<?php echo($var[1]($var[0])); ?>",$obj);
                }else
                    $obj = str_replace($label[$i][0],"<?php echo($var); ?>",$obj);
            }
        }
        # 传入参数为初始化状态，对代码段进行筛选过滤
        preg_match_all($this->VariableI, $obj, $label, PREG_SET_ORDER);
        # 迭代标记信息
        for($i=0; $i<count($label);$i++) {
            # 存在连接符号,拆分标记
            $var = str_replace(']', '', str_replace('[', '', $label[$i][0]));
            # 拆分变量
            if(strpos($var,".")){
                $var = explode(".",$var);
                $variable = null;
                for($i = 0;$i < count($var);$i++){
                    if(empty($i))
                        $variable = $var[$i];
                    elseif($i == count($var)-1){
                        # 验证拆分方法
                        if(strpos($var[$i],"|")){
                            $vars = explode("|",$var[$i]);
                            $function = $vars[1];
                            $variable .= "[\"$vars[0]\"]";
                            $obj = str_replace($label[$i][0],"$function($variable)",$obj);
                        }else{
                            $variable .= "[\"$var[$i]\"]";
                            $obj = str_replace($label[$i][0],"$variable",$obj);
                        }
                    }else{
                        if(preg_match("/^\[.+]$/",$var[$i]))
                            $variable .= $var[$i];
                        else
                            $variable .= "[\"$var[$i]\"]";
                    }
                }
            }else{
                # 验证拆分方法
                if(strpos($var,"|")){
                    $var = explode("|",$var);
                    $obj = str_replace($label[$i][0],"$var[1]($var[0])",$obj);
                }else
                    $obj = str_replace($label[$i][0],"$var",$obj);
            }
        }
        return $obj;
    }

    /**
     * 逻辑判断标签解释方法
     * @access protected
     * @param string $obj 解析代码段
     * @return string
    */
    function __if(string $obj): string
    {
        # 获取if标签
        $count = preg_match_all($this->JudgeIf,$obj , $IF, PREG_SET_ORDER);
        for($i = 0;$i < $count;$i++){
            # 获取条件内容
//            $condition =  preg_replace('/[\'\"]*/', '', $IF[$i][1]);
            $condition = substr($IF[$i][1],1,strlen($IF[$i][1])-2);
            # 拆分条件内容
            $condition = explode(" ",$condition);
            $symbol = $this->symbol($condition[1]);
            if(is_numeric($condition[2])){
                if(strpos($condition[2],"."))
                    $comparison = floatval($condition[2]);
                else
                    $comparison = intval($condition[2]);
            }else
                $comparison = strval($condition[2]);
            if($symbol == "in")
                $obj = str_replace($IF[$i][0],"<?php if(in_array($condition[0],$comparison)){?>",$obj);
            else
                $obj = str_replace($IF[$i][0],"<?php if($condition[0] $symbol $comparison){?>",$obj);
        }
        # 获取elseif标签
        $count = preg_match_all($this->JudgeEF, $obj, $EF, PREG_SET_ORDER);
        for($i = 0;$i < $count;$i++){
            # 获取条件内容
//            $condition =  preg_replace('/[\'\"]*/', '', $EF[$i][1]);
            $condition = substr($EF[$i][1],1,strlen($EF[$i][1])-2);
            # 拆分条件内容
            $condition = explode(" ",$condition);
            $symbol = $this->symbol($condition[1]);
            if(is_numeric($condition[2])){
                if(strpos($condition[2],"."))
                    $comparison = floatval($condition[2]);
                else
                    $comparison = intval($condition[2]);
            }else
                $comparison = strval($condition[2]);
            if($symbol == "in")
                $obj = str_replace($EF[$i][0],"<?php }elseif(in_array($condition[0],$comparison)){?>",$obj);
            else
                $obj = str_replace($EF[$i][0],"<?php }elseif($condition[0] $symbol $comparison){?>",$obj);
        }
        # 转义else逻辑语法
        if(preg_match_all($this->JudgeEl, $obj, $ELSE, PREG_SET_ORDER))
            $obj = str_replace($ELSE[0][0], "<?php }else{ ?>", $obj);
        # 转义if逻辑结尾标签
        if(preg_match_all($this->JudgeIe, $obj, $EIF, PREG_SET_ORDER))
            $obj = str_replace($EIF[0][0],"<?php } ?>",$obj);
        return $obj;
    }

    /**
     * 逻辑批处理方法
     * @access protected
     * @param string $symbol 运算符号
     * @return string
    */
    function symbol(string $symbol): string
    {
        $symbols = array(
            "gt" => ">","lt" => "<","ge" => ">=","le" => "<=",
            "heq" => "===","nheq" => "!==","eq" => "==","neq" => "!=",
            "in" => "in"
        );
        if(key_exists($symbol,$symbols))
            return $symbols[$symbol];
        else
            return "==";

    }

    /**
     * for标签结构解释方法
     * @access public
     * @param string $obj 进行解析的代码段
     * @return string
     */
    function __for(string $obj): string
    {
        # 获取当前代码段中是否存在foreach标签
        $count = preg_match_all($this->ForBegin, $obj, $begin, PREG_SET_ORDER);
        for($i = 0;$i < $count;$i++){
            $operate = preg_replace('/[\'\"]*/', '', $begin[$i][1]);
            $operate_i = "\$i_".str_replace("\$",null,$operate);
            $obj = str_replace($begin[$i][0],"<?php for($operate_i=0;$operate_i < count($operate);$operate_i++){ ?>",$obj);
            # 传入参数为初始化状态，对代码段进行筛选过滤
            preg_match_all($this->Variable, $obj, $label, PREG_SET_ORDER);
            # 迭代标记信息
            for($i=0; $i<count($label);$i++) {
                # 存在连接符号,拆分标记
                $var = str_replace('}', '', str_replace('{', '', $label[$i][0]));
                # 拆分变量
                if (strpos($var, ".")) {
                    $var = explode(".", $var);
                    if($var[0] == $operate){
                        $variable = null;
                        for($m = 0;$m < count($var);$m++){
                            if(empty($m))
                                $variable = "$var[$m][$operate_i]";
                            elseif($m == count($var)-1){
                                # 验证拆分方法
                                if(strpos($var[$m],"|")){
                                    $vars = explode("|",$var[$m]);
                                    $function = $vars[1];
                                    $variable .= "[\"$vars[0]\"]";
                                    $obj = str_replace($label[$i][0],"<?php echo($function($variable)); ?>",$obj);
                                }else{
                                    $variable .= "[\"$var[$m]\"]";
                                    $obj = str_replace($label[$i][0],"<?php echo($variable); ?>",$obj);
                                }
                            }else
                                $variable .= "[\"$var[$m]\"]";
                        }
                    }
                }
            }
            # 传入参数为初始化状态，对代码段进行筛选过滤
            preg_match_all($this->VariableI, $obj, $label, PREG_SET_ORDER);
            # 迭代标记信息
            for($i=0; $i<count($label);$i++) {
                # 存在连接符号,拆分标记
                $var = str_replace(']', '', str_replace('[', '', $label[$i][0]));
                # 拆分变量
                if (strpos($var, ".")) {
                    $var = explode(".", $var);
                    if($var[0] == $operate){
                        $variable = null;
                        for($m = 0;$m < count($var);$m++){
                            if(empty($m))
                                $variable = "$var[$m][$operate_i]";
                            elseif($m == count($var)-1){
                                # 验证拆分方法
                                if(strpos($var[$m],"|")){
                                    $vars = explode("|",$var[$m]);
                                    $function = $vars[1];
                                    $variable .= "[\"$vars[0]\"]";
                                    $obj = str_replace($label[$i][0],"$function($variable)",$obj);
                                }else{
                                    $variable .= "[\"$var[$m]\"]";
                                    $obj = str_replace($label[$i][0],"$variable",$obj);
                                }
                            }else
                                $variable .= "[\"$var[$m]\"]";
                        }
                    }
                }
            }
        }
        # 转义foreach逻辑结尾标签
        if(preg_match_all($this->ForEnd, $obj, $end, PREG_SET_ORDER))
            $obj = str_replace($end[0][0],"<?php } ?>",$obj);
        return $obj;
    }

    /**
     * foreach循环标签解释方法
     * @access protected
     * @param string $obj 解析代码段
     * @return string
    */
    function __foreach(string $obj): string
    {
        $count = preg_match_all($this->ForeachBegin, $obj, $begin, PREG_SET_ORDER);
        for($i = 0;$i < $count;$i++){
            $operate = preg_replace('/[\'\"]*/', '', $begin[$i][1]);
            if(strpos($operate," as ")){
                $operate = explode(" as ",$operate);
                $as_name = $operate[1];
                $operate = $operate[0];
                $obj = str_replace($begin[$i][0],"<?php foreach($operate as \$$as_name){ ?>",$obj);
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->Variable, $obj, $label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($label);$i++) {
                    # 存在连接符号,拆分标记
                    $var = str_replace('}', '', str_replace('{', '', $label[$i][0]));
                    # 拆分变量
                    if (strpos($var, ".")) {
                        $var = explode(".", $var);
                        if($var[0] == $as_name) {
                            $variable = null;
                            for ($m = 0; $m < count($var); $m++) {
                                if(empty($m))
                                    $variable = "$var[$m]";
                                elseif($m == count($var)-1){
                                    # 验证拆分方法
                                    if (strpos($var[$m], "|")) {
                                        $vars = explode("|", $var[$m]);
                                        $function = $vars[1];
                                        $variable .= "[\"$vars[0]\"]";
                                        $obj = str_replace($label[$i][0], "<?php echo($function($variable)); ?>", $obj);
                                    } else {
                                        $variable .= "[\"$var[$m]\"]";
                                        $obj = str_replace($label[$i][0], "<?php echo($variable); ?>", $obj);
                                    }
                                }else
                                    $variable .= "[\"$var[$m]\"]";
                            }
                        }
                    }
                }
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->VariableI, $obj, $label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($label);$i++) {
                    # 存在连接符号,拆分标记
                    $var = str_replace(']', '', str_replace('[', '', $label[$i][0]));
                    # 拆分变量
                    if (strpos($var, ".")) {
                        $var = explode(".", $var);
                        if($var[0] == $as_name) {
                            $variable = null;
                            for ($m = 0; $m < count($var); $m++) {
                                if(empty($m))
                                    $variable = "$var[$m]";
                                elseif($m == count($var)-1){
                                    # 验证拆分方法
                                    if(strpos($var[$m],"|")){
                                        $vars = explode("|",$var[$m]);
                                        $function = $vars[1];
                                        $variable .= "[\"$vars[0]\"]";
                                        $obj = str_replace($label[$i][0],"$function($variable)",$obj);
                                    }else{
                                        $variable .= "[\"$var[$m]\"]";
                                        $obj = str_replace($label[$i][0],"$variable",$obj);
                                    }
                                }else
                                    $variable .= "[\"$var[$m]\"]";
                            }
                        }
                    }
                }
            }else{
                $as = "{$operate}_i";
                $obj = str_replace($begin[$i][0],"<?php foreach($operate as $as){ ?>",$obj);
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->Variable, $obj, $label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($label);$i++) {
                    # 存在连接符号,拆分标记
                    $var = str_replace('}', '', str_replace('{', '', $label[$i][0]));
                    # 拆分变量
                    if (strpos($var, ".")) {
                        $var = explode(".", $var);
                        if($var[0] == $operate) {
                            $variable = null;
                            for ($m = 0; $m < count($var); $m++) {
                                if(empty($m))
                                    $variable = "$as";
                                elseif($m == count($var)-1){
                                    # 验证拆分方法
                                    if(strpos($var[$m],"|")){
                                        $vars = explode("|",$var[$m]);
                                        $function = $vars[1];
                                        $variable .= "[\"$vars[0]\"]";
                                        $obj = str_replace($label[$i][0],"<?php echo($function($variable)); ?>",$obj);
                                    }else{
                                        $variable .= "[\"$var[$m]\"]";
                                        $obj = str_replace($label[$i][0],"<?php echo($variable); ?>",$obj);
                                    }
                                }else
                                    $variable .= "[\"$var[$m]\"]";
                            }
                        }
                    }
                }
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->VariableI, $obj, $label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($label);$i++) {
                    # 存在连接符号,拆分标记
                    $var = str_replace(']', '', str_replace('[', '', $label[$i][0]));
                    # 拆分变量
                    if (strpos($var, ".")) {
                        $var = explode(".", $var);
                        if($var[0] == $operate) {
                            $variable = null;
                            for ($m = 0; $m < count($var); $m++) {
                                if(empty($m))
                                    $variable = "$as";
                                elseif($m == count($var)-1){
                                    # 验证拆分方法
                                    if(strpos($var[$m],"|")){
                                        $vars = explode("|",$var[$m]);
                                        $function = $vars[1];
                                        $variable .= "[\"$vars[0]\"]";
                                        $obj = str_replace($label[$i][0],"$function($variable)",$obj);
                                    }else{
                                        $variable .= "[\"$var[$m]\"]";
                                        $obj = str_replace($label[$i][0],"$variable",$obj);
                                    }
                                }else
                                    $variable .= "[\"$var[$m]\"]";
                            }
                        }
                    }
                }
            }
        }
        # 转义foreach逻辑结尾标签
        if(preg_match_all($this->ForeachEnd, $obj, $end, PREG_SET_ORDER))
            $obj = str_replace($end[0][0],"<?php } ?>",$obj);
        return $obj;
    }
}