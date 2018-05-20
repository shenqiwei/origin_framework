<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Mark.Label *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威*
 * create Time: 2017/02/03 16:04
 * update Time: 2018/02/18 16:04
 * chinese Context: IoC 标签二维解析器 (Origin)
 * 根据二维解释器程序结构特性，解释器会将一维结构中所有的应用逻辑进行数组降维展开，
 * 所以当数据维度超过一维结构时结构解释将返回null字节，同结构标签将无法完成维度解析
 * 该结构设计限制值针对企业定制框架模型及开源社区框架结构
 */
namespace Origin\Kernel\Mark;
# 调用标记接口
use Origin\Kernel\Mark\Impl\Label as Impl;
# 调用files控制类

/**
 * 标签解析主函数类
 */
class Origin implements Impl
{
    /**
     * 保存模板页信息变量
     * @var string $_Obj
     */
    private $_Obj = null;
    /**
     * 基础命名规则
     * @var string $_Basic_Regular
     */
    private $_Basic_Variable = '/^[^\_\W\s]+((\_|\-)?[^\_\W]+)*$/';
    /**
     * 列表数组键命名规则
     * @var string $_Array_Key
     */
    private $_Array_Key = '/\[\d+\]$/';
    /**
     * 带数组标记命名规则(增加应用标签结构)
     * @var string $_Basic_Array_Regular
     */
    private $_Basic_Array_Regular = '/^[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])$/';
    /**
     * 变量标记标签规则
     * @var string $_Var_Regular
     */
    private $_Variable = '/\{\$[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])?(\.[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])?)?(\s*\|\s*[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])?)?\}/';
    /**
     * 页面引入标签规则
     * @var string $_Include_Regular
    */
    private $_Include_Regular = '/\<o:include\s+href\s*=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?>/';
    /**
     * 逻辑判断标记规则
     * @var string $_Judge_Ci condition_information : 'variable eq conditions_variable'
     * @var string $_Judge_Si Symbol
     * @var string $_Judge_If if <o:if condition = 'variable eq conditions_variable'>
     * @var string $_Judge_EF elseif <o:elif condition = 'variable eq conditions_variable'/>
     * @var string $_Judge_El else <o:else/>
     * @var string $_Judge_El end </o:if>
     */
    private $_Judge_Si = '/\s(eq|gt|ge|lt|le|neq|heq|nheq\s)/';
    private $_Judge_If = '/\<o:if\s*condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $_Judge_EF = '/\<o:elif\s*condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?\>/';
    private $_Judge_El = '/\<o:else\s*\/\>/';
    private $_Judge_Ie = '/\<\/o:if\s*\>/';
    /**
     * 循环执行标签规则
     * @var string $_For_Operation 'variable to circulation_count'
     * @var string $_For_Begin <o:for operation = 'variable to circulation_count'>
     * @var string $_For_End </o:for> or <for:end>
     */
    private $_For_Operate = '/^.+(\s(to)\s.+(\s(by)\s.+)?)?$/';
    private $_For_Begin = '/\<o:for\s*operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $_For_End = '/\<[\/]o:for\s*\>/';
    /**
     * foreach循环标签规则
     * @var string $_Foreach_Operation 'variable (as mark_variable)'
     * @var string $_Foreach_Begin <o:foreach operation = ' variable (as mark_variable)'>
     * @var string $_Foreach_End </o:foreach>
     */
    private $_Foreach_Operate = '/^.+(\s(as)\s.+)?$/';
    private $_Foreach_Begin = '/\<o:foreach\s*operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private $_Foreach_End = '/\<[\/]o:foreach\s*\>/';
    /**
     * 对象数据存储
     * @var array $_Param_Array
     */
    private $_Param_Array = array();
    /**
     * 构造方法 获取引用页面地址信息
     * @access public
     * @param string $page
     * @param array $param
     */
    function __construct($page, $param)
    {
        $this->_Obj = $page;
        $this->_Param_Array = $param;
    }
    /**
     * 默认函数，用于对模板中标签进行转化和结构重组
     * @access public
     * @return string
     */
    function execute()
    {
        # 创建初始标签标记变量
        $_initialize = null;
        $_obj = file_get_contents($this->_Obj);
        # 转义引入结构
        $_obj = $this->include($_obj);
        # 判断初始标签结构,分析初始标签信息, 并定义分析器类型,包含一个合法条件
        while (true) {
            if ($_if_count = preg_match_all($this->_Judge_If, $_obj, $_if, PREG_SET_ORDER) > 0) $_initialize = 'judge';
            if ($_for_count = preg_match_all($this->_For_Begin, $_obj, $_for, PREG_SET_ORDER) > 0) $_initialize = 'traversal';
            if ($_loop_count = preg_match_all($this->_Foreach_Begin, $_obj, $_loop, PREG_SET_ORDER) > 0) $_initialize = 'loop';
            if ($_if_count == 0 and $_for_count == 0 and $_loop_count == 0) {
                break;
            }
            if ($_if_count > preg_match_all($this->_Judge_Ie, $_obj)) {
                try {
                    # 模板if else标签结构不完整
                    throw new \Exception('View File:' . $this->_Obj . ',Structure of label <if else> is not complete');
                } catch (\Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
            if ($_for_count > preg_match_all($this->_For_End, $_obj)) {
                try {
                    # 模板for标签结构不完整
                    throw new \Exception('View File:' . $this->_Obj . ',Structure of label <for> is not complete');
                } catch (\Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
            if ($_loop_count > preg_match_all($this->_Foreach_End, $_obj)) {
                try {
                    # 模板foreach标签结构不完整
                    throw new \Exception('View File:' . $this->_Obj . ',Structure of label <foreach> is not complete');
                } catch (\Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
            # 包含两个合法条件
            if ($_if_count > 0 and $_for_count > 0) {
                if (strpos($_obj, $_if[0][0]) < strpos($_obj, $_for[0][0])) {
                    $_initialize = 'judge';
                } else {
                    $_initialize = 'traversal';
                }
            }
            if ($_if_count > 0 and $_loop_count > 0) {
                if (strpos($_obj, $_if[0][0]) < strpos($_obj, $_loop[0][0])) {
                    $_initialize = 'judge';
                } else {
                    $_initialize = 'loop';
                }
            }
            if ($_for_count > 0 and $_loop_count > 0) {
                if (strpos($_obj, $_for[0][0]) < strpos($_obj, $_loop[0][0])) {
                    $_initialize = 'traversal';
                } else {
                    $_initialize = 'loop';
                }
            }
            # 包含全部合法条件
            if ($_if_count > 0 and $_for_count > 0 and $_loop_count > 0) {
                if (strpos($_obj, $_if[0][0]) < strpos($_obj, $_for[0][0])) {
                    if (strpos($_obj, $_if[0][0]) < strpos($_obj, $_loop[0][0])) {
                        $_initialize = 'judge';
                    } else {
                        $_initialize = 'loop';
                    }
                } else {
                    if (strpos($_obj, $_for[0][0]) < strpos($_obj, $_loop[0][0])) {
                        $_initialize = 'traversal';
                    } else {
                        $_initialize = 'loop';
                    }
                }
            }
            # 分析代码信息是否还存有标签结构
            if ($_if_count > 0 or $_for_count > 0 or $_loop_count > 0) {
                # 启动标签解释器
                $_obj = $this->$_initialize($_obj);
            }
        }
        # 转义变量标签
        $_obj = $this->variable($_obj);
        # 去除空白注释
        $_obj = preg_replace('/\<\!\-\-\s*\-\-\>/', "\r\n", str_replace('<!---->', '', $_obj));
        # 去多余标签结构
        $_obj = preg_replace($this->_For_End, '', $_obj);
        $_obj = preg_replace($this->_Foreach_End, '', $_obj);
        $_obj = preg_replace($this->_Judge_EF, '', $_obj);
        $_obj = preg_replace($this->_Judge_El, '', $_obj);
        $_obj = preg_replace($this->_Judge_Ie, '', $_obj);
        # 遍历资源目录，替换资源信息
        # 进行资源变量替换
        $_obj = str_replace('__JSCRIPT__', __HOST__ . __JSCRIPT__, $_obj);
        $_obj = str_replace('__MEDIA__', __HOST__ . __MEDIA__, $_obj);
        $_obj = str_replace('__STYLE__', __HOST__ . __STYLE__, $_obj);
        $_obj = str_replace('__TEMP__', __HOST__ . __TEMP__, $_obj);
        $_obj = str_replace('__PUBLIC__',__HOST__.__PUBLIC__, $_obj);
        $_obj = str_replace('__UPLOAD__',__HOST__.__UPLOAD__, $_obj);
        # 去去空白符结构
        $_obj = preg_replace('/\s+/', ' ', $_obj);
        return $_obj;
    }
    /**
     * 引入结构标签解释方法
     * @access protected
     * @param string $obj
     * @return string
    */
    function include($obj)
    {
        # 获取include标签信息
        $_count = preg_match_all($this->_Include_Regular, $obj, $_include, PREG_SET_ORDER);
        # 遍历include对象内容
        for($_i = 0;$_i < $_count; $_i++){
            # 拼接引入文件地址信息
            $_files = __PUBLIC__.'/'.str_replace('"','',$_include[$_i][1]);
            # 判断文件完整度
            if(indexFiles($_files)){
                # 读取引入对象内容
                $_mark = file_get_contents(ROOT.str_replace('/',SLASH,$_files));
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
     * @param string $variable
     * @param mixed $param
     * @param string $mapping
     * @return string
     */
    function variable($obj, $variable = null, $param = null, $mapping = null)
    {
        # 判断传入参数是否为初始化值
        if (is_null($param)) {
            # 传入参数为初始化状态，对代码段进行筛选过滤
            preg_match_all($this->_Variable, $obj, $_label, PREG_SET_ORDER);
            # 迭代标记信息
            for ($i = 0; $i < count($_label); $i++) {
                # 存在连接符号,拆分标记
                $_mark = str_replace('}', '', str_replace('{$', '', $_label[$i][0]));
                # 判断标记是否存在连接符号
                if (strpos($_mark, '.')) {
                    # 存在连接符号,拆分标记
                    $_mark = explode('.', $_mark);
                    # 判断标记结构与数据结构关系
                    if (array_key_exists($_mark[0], $this->_Param_Array)) {
                        # 映射结构数组
                        $_mapping = array();
                        # 加载对象数据
                        $_mapping[$_mark[0]] = $this->_Param_Array[$_mark[0]];
                        # 分析是否存在方法调用结构
                        if (strpos($_mark[1], '|')) {
                            # 对语法结构进行拆分
                            $_fun = explode('|', $_mark[1]);
                            # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                            if (preg_match($this->_Basic_Array_Regular, $_fun[0])) {
                                # 重复结构逻辑拆分结构
                                preg_match_all($this->_Array_Key, $_fun[0], $_num, PREG_SET_ORDER);
                                # 次级结构
                                $_array_key = intval(str_replace('[', '', str_replace(']', '', $_num[0][0])));
                                # 次级结构
                                $_array_var = str_replace($_num[0][0], '', $_fun[0]);
                                if (array_key_exists($_array_var, $_mapping[$_mark[0]])) {
                                    # 从数组中抽取，并赋值
                                    $_mapping[$_array_var] = $_mapping[$_mark[0]];
                                    if (is_array($_mapping[$_array_var][intval($_array_key)])) {
                                        $_value = null;
                                    } else {
                                        $_value = $_mapping[$_array_var][intval($_array_key)];
                                    }
                                } else {
                                    $_value = null;
                                }
                            } else {
                                if (is_array($_mapping[$_mark[0]][$_fun[0]])) {
                                    $_value = null;
                                } else {
                                    $_value = $_mapping[$_mark[0]][$_fun[0]];
                                }
                            }
                            $obj = str_replace($_label[$i][0], $this->assign(trim($_fun[1]), $_value), $obj);
                        } else {
                            # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                            if (preg_match($this->_Basic_Array_Regular, $_mark[1])) {
                                # 重复结构逻辑拆分结构
                                preg_match_all($this->_Array_Key, $_mark[1], $_num, PREG_SET_ORDER);
                                # 次级结构
                                $_array_key = intval(str_replace('[', '', str_replace(']', '', $_num[0][0])));
                                # 次级结构
                                $_array_var = str_replace($_num[0][0], '', $_mark[1]);
                                if (array_key_exists($_array_var, $_mapping[$_mark[0]])) {
                                    # 从数组中抽取，并赋值
                                    $_mapping[$_array_var] = $_mapping[$_mark[0]][$_array_var];
                                    if (is_array($_mapping[$_array_var][intval($_array_key)])) {
                                        $_value = null;
                                    } else {
                                        $_value = $_mapping[$_array_var][intval($_array_key)];
                                    }
                                } else {
                                    $_value = null;
                                }
                            } else {
                                if (is_array($_mapping[$_mark[0]][$_mark[1]])) {
                                    $_value = null;
                                } else {
                                    $_value = $_mapping[$_mark[0]][$_mark[1]];
                                }
                            }
                            $obj = str_replace($_label[$i][0], $_value, $obj);
                        }
                    } else {
                        # 判断变量结构是否存在调用数组结构内容
                        if (preg_match($this->_Basic_Array_Regular, $_mark[0])) {
                            # 抽取整数键值
                            preg_match_all($this->_Array_Key, $_mark[0], $_num, PREG_SET_ORDER);
                            # 赋予新的变量结构中
                            $_array_key = intval(str_replace('[', '', str_replace(']', '', $_num[0][0])));
                            # 拆分出数组请求名
                            $_array_var = str_replace($_num[0][0], '', $_mark[0]);
                            # 识别对象数组信息
                            if (array_key_exists($_array_var, $this->_Param_Array)) {
                                # 初始化映射对象
                                $_mapping = array();
                                # 加载对象数据
                                $_mapping[$_array_var] = $this->_Param_Array[$_array_var];
                                if (is_array($_mapping[$_array_var][intval($_array_key)])) {
                                    # 分析是否存在方法调用结构
                                    if (strpos($_mark[1], '|')) {
                                        # 对语法结构进行拆分
                                        $_fun = explode('|', $_mark[1]);
                                        # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                                        if (preg_match($this->_Basic_Array_Regular, $_fun[0])) {
                                            # 重复结构逻辑拆分结构
                                            preg_match_all($this->_Array_Key, $_fun[0], $_num, PREG_SET_ORDER);
                                            # 次级结构
                                            $_array_keys = intval(str_replace('[', '', str_replace(']', '', $_num[0][0])));
                                            # 次级结构
                                            $_array_vars = str_replace($_num[0][0], '', $_fun[0]);
                                            if (array_key_exists($_array_vars, $_mapping[$_array_var][intval($_array_key)])) {
                                                # 从数组中抽取，并赋值
                                                $_mapping[$_array_vars] = $_mapping[$_array_var][intval($_array_key)][$_array_vars];
                                                if (is_array($_mapping[$_array_vars][intval($_array_keys)])) {
                                                    $_value = null;
                                                } else {
                                                    $_value = $_mapping[$_array_vars][intval($_array_keys)];
                                                }
                                            } else {
                                                $_value = null;
                                            }
                                        } else {
                                            if (is_array($_mapping[$_mark[0]][$_fun[0]])) {
                                                $_value = null;
                                            } else {
                                                $_value = $_mapping[$_mark[0]][$_fun[0]];
                                            }
                                        }
                                        $obj = str_replace($_label[$i][0], $this->assign(trim($_fun[1]), $_value), $obj);
                                    } else {
                                        if (preg_match($this->_Basic_Array_Regular, $_mark[1])) {
                                            # 重复结构逻辑拆分结构
                                            preg_match_all($this->_Array_Key, $_mark[1], $_num, PREG_SET_ORDER);
                                            # 次级结构
                                            $_array_keys = intval(str_replace('[', '', str_replace(']', '', $_num[0][0])));
                                            # 次级结构
                                            $_array_vars = str_replace($_num[0][0], '', $_mark[1]);
                                            if (array_key_exists($_array_vars, $_mapping[$_array_var][intval($_array_key)])) {
                                                # 从数组中抽取，并赋值
                                                $_mapping[$_array_vars] = $_mapping[$_array_var][intval($_array_key)][$_array_vars];
                                                if (is_array($_mapping[$_array_vars][intval($_array_keys)])) {
                                                    $_value = null;
                                                } else {
                                                    $_value = $_mapping[$_array_vars][intval($_array_keys)];
                                                }
                                            } else {
                                                $_value = null;
                                            }
                                        } else {
                                            # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                                            if (is_array($_mapping[$_array_var][intval($_array_key)][$_mark[1]])) {
                                                $_value = null;
                                            } else {
                                                $_value = $_mapping[$_array_var][intval($_array_key)][$_mark[1]];
                                            }
                                        }
                                        $obj = str_replace($_label[$i][0], $_value, $obj);
                                    }
                                }
                            } else {
                                $obj = str_replace($_label[$i][0], null, $obj);
                            }
                        }
                    }
                } else {
                    # 分析是否存在方法调用结构
                    if (strpos($_mark, '|')) {
                        # 对语法结构进行拆分
                        $_fun = explode('|', $_mark);
                        # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                        if (preg_match($this->_Basic_Array_Regular, $_fun[0])) {
                            # 重复结构逻辑拆分结构
                            preg_match_all($this->_Array_Key, $_fun[0], $_num, PREG_SET_ORDER);
                            # 次级结构
                            $_array_key = intval(str_replace('[', '', str_replace(']', '', $_num[0][0])));
                            # 次级结构
                            $_array_var = str_replace($_num[0][0], '', $_fun[0]);
                            if (array_key_exists($_array_var, $this->_Param_Array)) {
                                # 从数组中抽取，并赋值
                                $_mapping[$_array_var] = $this->_Param_Array[$_array_var];
                                if (is_array($_mapping[$_array_var][intval($_array_key)])) {
                                    $_value = null;
                                } else {
                                    $_value = $_mapping[$_array_var][intval($_array_key)];
                                }
                            } else {
                                $_value = null;
                            }
                        } else {
                            if (is_array($this->_Param_Array[trim($_fun[0])])) {
                                $_value = null;
                            } else {
                                $_value = $this->_Param_Array[trim($_fun[0])];
                            }
                        }
                        $obj = str_replace($_label[$i][0], $this->assign(trim($_fun[1]), $_value), $obj);
                    } else {
                        # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                        if (preg_match($this->_Basic_Array_Regular, $_mark)) {
                            # 重复结构逻辑拆分结构
                            preg_match_all($this->_Array_Key, $_mark, $_num, PREG_SET_ORDER);
                            # 次级结构
                            $_array_key = intval(str_replace('[', '', str_replace(']', '', $_num[0][0])));
                            # 次级结构
                            $_array_var = str_replace($_num[0][0], '', $_mark);
                            if (array_key_exists($_array_var, $this->_Param_Array)) {
                                # 从数组中抽取，并赋值
                                $_mapping[$_array_var] = $this->_Param_Array[$_array_var];
                                if (is_array($_mapping[$_array_var][intval($_array_key)])) {
                                    $_value = null;
                                } else {
                                    $_value = $_mapping[$_array_var][intval($_array_key)];
                                }
                            } else {
                                $_value = null;
                            }
                        } else {
                            if (is_array($this->_Param_Array[$_mark])) {
                                $_value = null;
                            } else {
                                $_value = $this->_Param_Array[$_mark];
                            }
                        }
                        $obj = str_replace($_label[$i][0], $_value, $obj);
                    }
                }
            }
        } else {
            # 替换掉变量标记符号
            $_mark = str_replace('}', '', str_replace('{$', '', trim($variable)));
            # 判断数组变量结构
            if (strpos($_mark, '.')) {
                # 拆分变量信息结构爱
                $_var = explode('.', $_mark);
                if (trim($_var[0]) == $mapping) {
                    # 分析是否存在方法调用结构
                    if (strpos($_var[1], '|')) {
                        # 对语法结构进行拆分
                        $_fun = explode('|', $_var[1]);
                        # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                        if (is_array($param[trim($_fun[0])])) {
                            $_value = null;
                        } else {
                            $_value = $param[trim($_fun[0])];
                        }
                        $obj = str_replace($variable, $this->assign(trim($_fun[1]), $_value), $obj);
                    } else {
                        # 转义对应标签 , 一维逻辑控制 , 强制一维逻辑限制
                        if (is_array($param[trim($_var[1])])) {
                            $_value = null;
                        } else {
                            $_value = $param[trim($_var[1])];
                        }
                        $obj = str_replace($variable, $_value, $obj);
                    }
                }
            }
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
    function judge($obj, $dr = true)
    {
        # 获取if起始标签
        $_count = preg_match_all($this->_Judge_If, $obj, $_if, PREG_SET_ORDER);
        # 获取if结束标签
        preg_match_all($this->_Judge_Ie, $obj, $_end, PREG_SET_ORDER);
        if ($dr === true) {
            # 当前代码段进行截取
            $_judge = substr($obj, strpos($obj, $_if[0][0]), strrpos($obj, $_end[0][0]) - (strpos($obj, $_if[0][0]) - strlen($_end[0][0])));
            # 等待验证结构
            $_waiting = $_judge;
            # 已解析完成结构
            $_analysis = null;
            # 起始(结束)标签数量，用于检测维度结构
            $_begin_num = 0; // 起始标签数量
            $_end_num = 0; // 结束标签数量
            # 初始化指针
            $_i = 0;
            # 迭代for标签数组
            while (true) {
                /* 使用沙漏算法对结构进行一维结构验证 */
                if ($_count == 1) {
                    /* 一度降维需要对末尾标签进行规则验证, 由于实行标签首尾结构对称原则，所以最后一个出现起始标记无需验证可以直接执行 */
                    $_analysis = $this->judge($_judge, false);
                    break;
                } else {
                    # 判断中间结构中是否存在结束标签
                    if (strpos($_waiting, $_end[0][0]) < strpos($_waiting, $_if[$_i][0]) or $_i == $_count) {
                        # 将代码截取放入待解析结构中
                        $_analysis .= substr($_waiting, 0, strpos($_waiting, $_end[0][0]) + strlen($_end[0][0]));
                        # 维度结构有差异时，将结构再次截取，并拼接变更结构
                        $_waiting = substr($_waiting, strpos($_waiting, $_end[0][0]) + strlen($_end[0][0]));
                        # 结束标签数量自增1，为分析维度结构做参照
                        $_end_num += 1;
                    } else {
                        # 非末尾标签
                        $_analysis .= substr($_waiting, 0, strpos($_waiting, $_if[$_i][0]) + strlen($_if[$_i][0]));
                        $_waiting = substr($_waiting, strpos($_waiting, $_if[$_i][0]) + strlen($_if[$_i][0]));
                        # 当中间结构没有结束标签时, 起始标签自增1，为分析维度结构做参照
                        $_begin_num += 1;
                        # 指针下移
                        $_i += 1;
                    }
                    if ($_end_num == $_begin_num and $_end_num > 0) {
                        $_analysis = $this->judge($_analysis, false) . $_waiting;
                        break;
                    }
                }
            }
            $obj = str_replace($_judge, $_analysis, $obj);
        } else {
            # 对当前代码段进行截取
            $_judge = substr($obj, strpos($obj, $_if[0][0]), strrpos($obj, $_end[0][0]) - (strpos($obj, $_if[0][0]) - strlen($_end[0][0])));
            # 过滤foreach标签及空白字符
            /**二维语法设置点,使用二维语法*/
            $_waiting = trim(preg_replace('/[^\S][\s]+[^\S]/', ' ', substr($_judge, strlen($_if[0][0]), (strlen($_judge) - strlen($_if[0][0]) - strlen($_end[0][0])))), '\t\n\r ');
            # 逻辑运算状态值
            $_status = false;
            # 分析代码段中是否包含elseif语法
            $_ef_count = preg_match_all($this->_Judge_EF, $_judge, $_ef, PREG_SET_ORDER);
            # 分析代码段中是否包含else语法
            $_el_count = preg_match_all($this->_Judge_El, $_judge, $_el, PREG_SET_ORDER);
            # 对if标签内建属性进行分析，处理，并将对应数据信息存入中间数组中
            $_operate = preg_replace('/[\'\"]*/', '', $_if[0][1]);
            # 判断属性信息中是否包含运算符号
            if (preg_match_all($this->_Judge_Si, $_operate, $_re, PREG_SET_ORDER)) {
                # 将字符串根据条件公式转为数组
                $_variable = explode(trim($_re[0][0]), $_operate);
                # 分析当前代码是否为变量标签
                if (preg_match($this->_Variable, trim($_variable[0]))) {
                    $_condition = $this->variable(trim($_variable[0]));
                } else {
                    $_condition = trim($_variable[0]);
                }
                # 分析当前代码结构是否为变量标签
                if (preg_match($this->_Variable, trim($_variable[1]))) {
                    $_param = $this->variable(trim($_variable[1]));
                } else {
                    $_param = trim($_variable[1]);
                }
                # 调用逻辑判断函数，判断回传信息
                /**
                 * bool调用位置
                 */
                if ($this->bool(trim($_re[0][0]), $_condition, $_param) === true) {
                    $_status = true;
                } else {
                    # if验证为false，验证代码结构中是否包含子语法
                    if ($_ef_count > 0) {
                        # 包含elseif语法结构
                        $_waiting = substr($_waiting, strpos($_waiting, $_ef[0][0]));
                    } else {
                        # 只包含else语法结构
                        if ($_el_count > 0) {
                            $_waiting = substr($_waiting, strpos($_waiting, $_el[0][0]));
                        } else {
                            # 只有if语法结构
                            $_waiting = '';
                        }
                    }
                }
            }
            if ($_status === false and $_ef_count > 0) {
                for ($i = 0; $i < $_ef_count; $i++) {
                    if ($_status === false) {
                        if ($i === $_ef_count - 1) {
                            # 代码段中包含else语法
                            if ($_el_count > 0) {
                                # 获取当前elseif代码结构
                                $_elseif = substr($_waiting, strrpos($_waiting, $_ef[$i][0]), strpos($_waiting, $_el[0][0]) - strrpos($_waiting, $_ef[$i][0]) - 1);
                                # 去除elseif语法结构
                                /**二维语法设置点,使用二维语法*/
                                $_elseif_code = trim(preg_replace('/[^\S][\s]+[^\S]/', ' ', substr($_elseif, strlen($_ef[$i][0]))), '\t\n\r ');
                                # 抽取条件信息
                                $_elseif_operate = preg_replace('/[\'\"]*/', '', $_ef[$i][1]);
                                if (preg_match_all($this->_Judge_Si, $_elseif_operate, $_re, PREG_SET_ORDER)) {
                                    # 将字符串根据条件公式转为数组
                                    $_variable = explode(trim($_re[0][0]), $_elseif_operate);
                                    # 分析当前代码是否为变量标签
                                    if (preg_match($this->_Variable, trim($_variable[0]))) {
                                        $_condition = $this->variable(trim($_variable[0]));
                                    } else {
                                        $_condition = trim($_variable[0]);
                                    }
                                    # 分析当前代码结构是否为变量标签
                                    if (preg_match($this->_Variable, trim($_variable[1]))) {
                                        $_param = $this->variable(trim($_variable[1]));
                                    } else {
                                        $_param = trim($_variable[1]);
                                    }
                                    # 调用逻辑判断函数，判断回传信息
                                    /**
                                     * bool调用位置
                                     */
                                    if ($this->bool(trim($_re[0][0]), $_condition, $_param) === true) {
                                        $_status = true;
                                        $_waiting = $_elseif_code;
                                    } else {
                                        $_waiting = str_replace($_elseif, '', $_waiting);
                                    }
                                }
                            } else {
                                # 代码段中无else语法
                                $_elseif = substr($_waiting, strrpos($_waiting, $_ef[$i][0]));
                                # 获取当前elseif代码结构
                                /**二维语法设置点,使用二维语法*/
                                $_elseif_code = trim(preg_replace('/[^\S][\s]+[^\S]/', ' ', substr($_elseif, strlen($_el[$i][0]), strlen($_elseif) - strlen($_el[$i][0]) - strlen($_end[0][0]))), '\t\n\r ');
                                # 抽取条件信息
                                $_elseif_operate = preg_replace('/[\'\"]*/', '', $_ef[$i][1]);
                                if (preg_match_all($this->_Judge_Si, $_elseif_operate, $_re, PREG_SET_ORDER)) {
                                    # 将字符串根据条件公式转为数组
                                    $_variable = explode(trim($_re[0][0]), $_elseif_operate);
                                    # 分析当前代码是否为变量标签
                                    if (preg_match($this->_Variable, trim($_variable[0]))) {
                                        $_condition = $this->variable(trim($_variable[0]));
                                    } else {
                                        $_condition = trim($_variable[0]);
                                    }
                                    # 分析当前代码结构是否为变量标签
                                    if (preg_match($this->_Variable, trim($_variable[1]))) {
                                        $_param = $this->variable(trim($_variable[1]));
                                    } else {
                                        $_param = trim($_variable[1]);
                                    }
                                    # 调用逻辑判断函数，判断回传信息
                                    /**
                                     * bool调用位置
                                     */
                                    if ($this->bool(trim($_re[0][0]), $_condition, $_param) === true) {
                                        $_status = true;
                                        $_waiting = $_elseif_code;
                                    } else {
                                        $_waiting = str_replace($_elseif, '', $_waiting);
                                    }
                                }
                            }
                        } else {
                            # 出现连续相同elseif语法标签
                            if ($_ef[$i][0] == $_ef[$i + 1][0]) {
                                # 删掉当前标签并跳过当前解析过程
                                $_waiting = substr($_waiting, strpos($_waiting, $_ef[$i][0]) + strlen($_ef[$i][0]));
                                continue;
                            }
                            # 获取当前elseif代码结构
                            $_elseif = substr($_waiting, strpos($_waiting, $_ef[$i][0]), strpos($_waiting, $_ef[$i + 1][0]) - strpos($_waiting, $_ef[$i][0]) - 1);
                            # 去除elseif语法结构
                            /**二维语法设置点,使用二维语法*/
                            $_elseif_code = trim(preg_replace('/[^\S][\s]+[^\S]/', ' ', substr($_elseif, strlen($_ef[$i][0]))), '\t\n\r ');
                            # 抽取条件信息
                            $_elseif_operate = preg_replace('/[\'\"]*/', '', $_ef[$i][1]);
                            # 如果存在逻辑运算符号,存入$_re
                            if (preg_match_all($this->_Judge_Si, $_elseif_operate, $_re, PREG_SET_ORDER)) {
                                # 将字符串根据条件公式转为数组
                                $_variable = explode(trim($_re[0][0]), $_elseif_operate);
                                # 分析当前代码是否为变量标签
                                if (preg_match($this->_Variable, trim($_variable[0]))) {
                                    $_condition = $this->variable(trim($_variable[0]));
                                } else {
                                    $_condition = trim($_variable[0]);
                                }
                                # 分析当前代码结构是否为变量标签
                                if (preg_match($this->_Variable, trim($_variable[1]))) {
                                    $_param = $this->variable(trim($_variable[1]));
                                } else {
                                    $_param = trim($_variable[1]);
                                }
                                # 调用逻辑判断函数，判断回传信息
                                /**
                                 * bool调用位置
                                 */
                                if ($this->bool(trim($_re[0][0]), $_condition, $_param) === true) {
                                    $_status = true;
                                    $_waiting = $_elseif_code;
                                } else {
                                    $_waiting = str_replace($_elseif, '', $_waiting);
                                }
                            }
                        }
                    }
                }
            } else {
                # 代码段中有elseif语法结构
                if ($_ef_count > 0) {
                    $_waiting = str_replace(substr($_waiting, strpos($_waiting, $_ef[0][0])), '', $_waiting);
                }
            }
            # 判断上层逻辑是否已经完成执行，当逻辑判断执行都处于false状态时进行else语法判断
            if ($_status === false and $_el_count > 0) {
                $_else_code = str_replace($_el[count($_el) - 1][0], '', substr($_waiting, strripos($_waiting, $_el[count($_el) - 1][0])));
                $_waiting = $_else_code;
            } else {
                # 代码段中有else语法结构,由于在elseif语法结构部分对else有进行连带操作，所以在else结构中需要再次验证else语法是否存在
                if ($_el_count > 0 and preg_match($this->_Judge_El, $_waiting)) {
                    $_waiting = str_replace(substr($_waiting, strripos($_waiting, $_el[count($_el) - 1][0])), '', $_waiting);
                }
            }
            $obj = str_replace($_judge, $_waiting, $obj);
        }
        return $obj;
    }
    /**
     * 逻辑批处理方法
     * @access protected
     * @param string $symbol
     * @param string $var
     * @param string $param
     * @return boolean;
     */
    function bool($symbol, $var, $param)
    {
        # 创建初始返回信息变量
        $_receipt = false;
        # 使用switch语法进行精确结构验证,并将上层结构语法转化为php基础运算语法
        switch ($symbol) {
            case 'neq':
                if ($var != $param) $_receipt = true;
                break;
            case 'gt':
                if ($var > $param) $_receipt = true;
                break;
            case 'lt':
                if ($var < $param) $_receipt = true;
                break;
            case 'ge':
                if ($var >= $param) $_receipt = true;
                break;
            case 'le':
                if ($var <= $param) $_receipt = true;
                break;
            case 'heq':
                if ($var === $param) $_receipt = true;
                break;
            case 'nheq':
                if ($var !== $param) $_receipt = true;
                break;
            case 'eq':
            default:
                if ($var == $param) $_receipt = true;
                break;
        }
        return $_receipt;
    }
    /**
     * for循环标签解释方法
     * @access protected
     * @param string $obj
     * @param boolean $dr
     * @return string
     */
    function traversal($obj, $dr = true)
    {
        $_count = preg_match_all($this->_For_Begin, $obj, $_begin, PREG_SET_ORDER);
        # 获取当前代码中的foreach结束标签
        preg_match_all($this->_For_End, $obj, $_end, PREG_SET_ORDER);
        if ($dr === true) {
            # 当前代码段进行截取
            $_for = substr($obj, strpos($obj, $_begin[0][0]), strrpos($obj, $_end[0][0]) - (strpos($obj, $_begin[0][0]) - strlen($_end[0][0])));
            # 等待验证结构
            $_waiting = $_for;
            # 已解析完成结构
            $_analysis = null;
            # 起始(结束)标签数量，用于检测维度结构
            $_begin_num = 0; // 起始标签数量
            $_end_num = 0; // 结束标签数量
            # 初始化指针
            $_i = 0;
            # 迭代for标签数组
            while (true) {
                # 当指针移动空位时，停止循环，该代码段只用于结构支撑，无实际意义，测试版之后会予以删除
                if ($_begin_num == $_end_num and $_end_num > 0) break;
                /* 使用沙漏算法对结构进行一维结构验证 */
                if ($_count == 1) {
                    /* 一度降维需要对末尾标签进行规则验证, 由于实行标签首尾结构对称原则，所以最后一个出现起始标记无需验证可以直接执行 */
                    $_analysis = $this->traversal($_for, false);
                    break;
                } else {
                    # 判断中间结构中是否存在结束标签
                    if (strpos($_waiting, $_end[0][0]) < strpos($_waiting, $_begin[$_i][0]) or $_i == $_count) {
                        # 将代码截取放入待解析结构中
                        $_analysis .= substr($_waiting, 0, strpos($_waiting, $_end[0][0]) + strlen($_end[0][0]));
                        # 维度结构有差异时，将结构再次截取，并拼接变更结构
                        $_waiting = substr($_waiting, strpos($_waiting, $_end[0][0]) + strlen($_end[0][0]));
                        # 结束标签数量自增1，为分析维度结构做参照
                        $_end_num += 1;
                    } else {
                        # 非末尾标签
                        $_analysis .= substr($_waiting, 0, strpos($_waiting, $_begin[$_i][0]) + strlen($_begin[$_i][0]));
                        $_waiting = substr($_waiting, strpos($_waiting, $_begin[$_i][0]) + strlen($_begin[$_i][0]));
                        # 当中间结构没有结束标签时, 起始标签自增1，为分析维度结构做参照
                        $_begin_num += 1;
                        # 指针下移
                        $_i += 1;
                    }
                    if ($_end_num == $_begin_num and $_end_num > 0) {
                        $_analysis = $this->traversal($_analysis, false) . $_waiting;
                        break;
                    }
                }
            }
            # 替换解析结构
            $obj = str_replace($_for, $_analysis, $obj);
        } else {
            preg_match_all($this->_For_End, $obj, $_end, PREG_SET_ORDER);
            # 对当前代码段进行截取
            $_for = substr($obj, strpos($obj, $_begin[0][0]), strrpos($obj, $_end[0][0]) - (strpos($obj, $_begin[0][0]) - strlen($_end[0][0])));
            # 等待解析结构
            /**二维语法设置点,使用二维语法*/
            $_waiting = trim(preg_replace('/[^\S][\s]+[^\S]/', ' ', substr($_for, strlen($_begin[0][0]), strlen($_for) - strlen($_begin[0][0]) - strlen($_end[0][0]))), '\t\n\r ');
            # 映射对象数组
            $_mapping = array();
            # 初始循环限制变量
            $_num = 0;
            # 判断变量标签是否存在，并判断语法合法性
            if (preg_match_all($this->_Variable, $_waiting, $_variable, PREG_SET_ORDER)) {
                # 对for标签内建属性进行分析，处理，并将对应数据信息存入中间数组中
                $_operate = preg_replace('/[\'\"]*/', '', $_begin[0][1]);
                # 判断内建属性信息合法性
                if (preg_match_all($this->_For_Operate, $_operate)) {
                    # 判断转义参数是否存在
                    if (strpos($_operate, 'to')) {
                        # 拆分属性信息
                        $_operate = explode('to', $_operate);
                        $_mapping['mapping_key'] = trim($_operate[0]);
                        $_mapping[$_mapping['mapping_key']] = $this->_Param_Array[$_mapping['mapping_key']];
                        if (intval(trim($_operate[1]) <= count($_mapping[$_mapping['mapping_key']]))) {
                            $_num = intval(trim($_operate[1]));
                        } else {
                            $_num = count($_mapping[$_mapping['mapping_key']]);
                        }
                    } else {
                        # 直接装载数据
                        $_mapping['mapping_key'] = trim($_operate);
                        $_mapping[$_mapping['mapping_key']] = $this->_Param_Array[$_mapping['mapping_key']];
                        $_num = count($_mapping[$_mapping['mapping_key']]);
                    }
                }
                # 创建信息中间变量
                $_obj = null;
                # 遍历对象数组
                for ($_i = 0; $_i < $_num; $_i++) {
                    # 创建中间结构变量
                    $_receipt = $_waiting;
                    # 遍历循环参数变量
                    for ($i = 0; $i < count($_variable); $i++) {
                        # 判断变量标签合法性
                        if (preg_match_all($this->_Variable, $_variable[$i][0])) {
                            # 调用变量标签方法，进行标签信息转化
                            $_receipt = $this->variable($_receipt, $_variable[$i][0], $_mapping[$_mapping['mapping_key']][$_i], $_mapping['mapping_key']);
                        }
                    }
                    $_obj .= $_receipt;
                }
                # 替换原标签结构
                $obj = str_replace($_for, $_obj, $obj);
            } else {
                # 替换原标签结构
                $obj = str_replace($_for, '', $obj);
            }
        }
        return $obj;
    }
    /**
     * foreach标签结构解释方法
     * @access public
     * @param string $obj 进行解析的代码段
     * @param boolean $dr 是否进行解维运算默认true(进行解维运算)
     * @return string
     */
    function loop($obj, $dr = true)
    {
        # 获取当前代码段中是否存在foreach标签
        $_count = preg_match_all($this->_Foreach_Begin, $obj, $_begin, PREG_SET_ORDER);
        # 获取当前代码中的foreach结束标签
        preg_match_all($this->_Foreach_End, $obj, $_end, PREG_SET_ORDER);
        # 根据维度运算要求进行下阶段运算
        if ($dr === true) {
            # 当前代码段进行截取
            $_foreach = substr($obj, strpos($obj, $_begin[0][0]), strrpos($obj, $_end[0][0]) - (strpos($obj, $_begin[0][0]) - strlen($_end[0][0])));
            # 等待验证结构
            $_waiting = $_foreach;
            # 已解析完成结构
            $_analysis = null;
            # 起始(结束)标签数量，用于检测维度结构
            $_begin_num = 0; // 起始标签数量
            $_end_num = 0; // 结束标签数量
            # 指针变量
            $_i = 0;
            # 迭代foreach标签数组
            while (true) {
                # 当指针移动空位时，停止循环，该代码段只用于结构支撑，无实际意义，测试版之后会予以删除
                if ($_begin_num == $_end_num and $_end_num > 0) break;
                # 将同结构标签放入解析单元中进行解析
                if ($_count == 1) {
                    /*一度降维需要对末尾标签进行规则验证, 由于实行标签首尾结构对称原则，所以最后一个出现起始标记无需验证可以直接执行 */
                    $_analysis = $this->loop($_foreach, false);
                    break;
                } else {
                    # 判断中间结构中是否存在结束标签
                    if (strpos($_waiting, $_end[0][0]) < strpos($_waiting, $_begin[$_i][0]) or $_i == $_count) {
                        # 将代码截取放入待解析结构中
                        $_analysis .= substr($_waiting, 0, strpos($_waiting, $_end[0][0]) + strlen($_end[0][0]));
                        # 维度结构有差异时，将结构再次截取，并拼接变更结构
                        $_waiting = substr($_waiting, strpos($_waiting, $_end[0][0]) + strlen($_end[0][0]));
                        # 结束标签数量自增1，为分析维度结构做参照
                        $_end_num += 1;
                    } else {
                        # 非末尾标签
                        $_analysis .= substr($_waiting, 0, strpos($_waiting, $_begin[$_i][0]) + strlen($_begin[$_i][0]));
                        $_waiting = substr($_waiting, strpos($_waiting, $_begin[$_i][0]) + strlen($_begin[$_i][0]));
                        # 当中间结构没有结束标签时, 起始标签自增1，为分析维度结构做参照
                        $_begin_num += 1;
                        # 指针下移
                        $_i += 1;
                    }
                    if ($_end_num == $_begin_num and $_end_num > 0) {
                        $_analysis = $this->loop($_analysis, false) . $_waiting;
                        break;
                    }
                }
            }
            # 替换解析结构
            $obj = str_replace($_foreach, $_analysis, $obj);
        } else {
            # 当前代码段进行截取
            $_foreach = substr($obj, strpos($obj, $_begin[0][0]), strrpos($obj, $_end[0][0]) - (strpos($obj, $_begin[0][0]) - strlen($_end[0][0])));
            # 等待解析结构
            /**二维语法设置点,使用二维语法*/
            $_waiting = trim(preg_replace('/[^\S][\s]+[^\S]/', ' ', substr($_foreach, strlen($_begin[0][0]), strlen($_foreach) - strlen($_begin[0][0]) - strlen($_end[0][0]))), '\t\n\r ');
            # 映射对象数组
            $_mapping = array();
            # 判断变量标签是否存在，并判断语法合法性
            if (preg_match_all($this->_Variable, $_waiting, $_variable, PREG_SET_ORDER)) {
                # 对foreach标签内建属性进行分析，处理，并将对应数据信息存入中间数组中
                $_operate = preg_replace('/[\'\"]*/', '', $_begin[0][1]);
                # 判断内建属性信息合法性
                if (preg_match_all($this->_Foreach_Operate, $_operate)) {
                    # 判断转义参数是否存在
                    if (strpos($_operate, 'as')) {
                        # 拆分属性信息
                        $_operate = explode('as', $_operate);
                        $_mapping['mapping_key'] = trim($_operate[1]);
                        $_mapping[$_mapping['mapping_key']] = $this->_Param_Array[trim($_operate[0])];
                    } else {
                        # 直接装载数据
                        $_mapping['mapping_key'] = trim($_operate);
                        $_mapping[$_mapping['mapping_key']] = $this->_Param_Array[trim($_operate)];
                    }
                }
                # 创建信息中间变量
                $_obj = null;
                # 遍历对象数组
                for ($_i = 0; $_i < count($_mapping[$_mapping['mapping_key']]); $_i++) {
                    # 创建中间结构变量
                    $_receipt = $_waiting;
                    # 遍历循环参数变量
                    for ($i = 0; $i < count($_variable); $i++) {
                        # 判断变量标签合法性
                        if (preg_match_all($this->_Variable, $_variable[$i][0])) {
                            # 调用变量标签方法，进行标签信息转化
                            $_receipt = $this->variable($_receipt, $_variable[$i][0], $_mapping[$_mapping['mapping_key']][$_i], $_mapping['mapping_key']);
                        }
                    }
                    $_obj .= $_receipt;
                }
                # 替换原标签结构
                $obj = str_replace($_foreach, $_obj, $obj);
            } else {
                # 替换原标签结构
                $obj = str_replace($_foreach, '', $obj);
            }
        }
        return $obj;
    }

    /**
     * 函数动态调用解释方法
     * @access protected
     * @param string $function
     * @param string $param
     * @return string
     */
    function assign($function, $param)
    {
        # 创建返回值变量
        # 判断函数名基本命名规则是否符合标记要求
        $_obj = null;
        if (is_true($this->_Basic_Variable, $function) === true) {
            # 调用函数方法, 被调用函数必须包含返回值,值类型不能是对象（或者数组，数组涉及超二维运算）
            $_obj = $function($param);
            # 当返回值为对象或者数组时，返回值为空
            if (is_object($_obj) or is_array($_obj)) {
                $_obj = null;
            }
        }
        return $_obj;
    }
}