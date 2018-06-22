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
 * partner: 沈启威 *
 * create Time: 2017/02/03 16:04
 * update Time: 2018/02/18 16:04
 * chinese Context: IoC 标签二维解析器
 * 根据二维解释器程序结构特性，解释器会将一维结构中所有的应用逻辑进行数组降维展开，
 * 所以当数据维度超过一维结构时结构解释将返回null字节，同结构标签将无法完成维度解析
 * 该结构设计限制值针对企业定制框架模型及开源社区框架结构
 */
namespace Origin\Kernel\Mark;
/**
 * 标签解析主函数类
 */
class Label extends Analysis
{
    /**
     * 保存模板页信息变量
     * @var string $_Obj
    */
    protected $_Obj = null;
    /**
     * 基础命名规则
     * @var string $_Basic_Regular
     */
    protected $_Basic_Variable = '/^[^\_\W\s]+((\_|\-)?[^\_\W]+)*$/';
    /**
     * 列表数组键命名规则
     * @var string $_Array_Key
     */
    protected $_Array_Key = '/\[\d+\]$/';
    /**
     * 带数组标记命名规则(增加应用标签结构)
     * @var string $_Basic_Array_Regular
     */
    protected $_Basic_Array_Regular = '/^[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])$/';
    /**
     * 变量标记标签规则
     * @var string $_Var_Regular
    */
    protected $_Variable = '/\{\$[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])?(\.[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])?)?(\s*\|\s*[^\_\W\s]+((\_|\-)?[^\_\W]+)*(\[\d+\])?)?\}/';
    /**
     * 页面引入标签规则
     * @var string $_Include_Regular <include href="src/html/page.html"/>
     */
    protected $_Include_Regular = '/\<include\s+href\s*=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?>/';
    /**
     * 逻辑判断标记规则
     * @var string $_Judge_Ci condition_information : 'variable eq conditions_variable'
     * @var string $_Judge_Si Symbol
     * @var string $_Judge_If if <if condition = 'variable eq conditions_variable'>
     * @var string $_Judge_EF elseif <elseif condition = 'variable eq conditions_variable'/>
     * @var string $_Judge_El else <else/>
     * @var string $_Judge_El end </if>
     */
    protected $_Judge_Si ='/\s(eq|gt|ge|lt|le|neq|heq|nheq|in)\s/';
    protected $_Judge_If = '/\<if\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    protected $_Judge_EF = '/\<elseif\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?\>/';
    protected $_Judge_El = '/\<else[\/]?\>/';
    protected $_Judge_Ie = '/\<[\/]if\s*\>/';
    /**
     * 循环执行标签规则
     * @var string $_For_Operation 'variable to circulation_count'
     * @var string $_For_Begin <for operation = 'variable to circulation_count'>
     * @var string $_For_End </for>
     */
    protected $_For_Operate = '/^.+(\s(to)\s.+(\s(by)\s.+)?)?$/';
    protected $_For_Begin = '/\<for\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    protected $_For_End = '/\<[\/]for\s*\>/';
    /**
     * foreach循环标签规则
     * @var string $_Foreach_Operation 'variable (as mark_variable)'
     * @var string $_Foreach_Begin <foreach operation = '(as mark_variable)'>
     * @var string $_Foreach_End </foreach>
     */
    protected $_Foreach_Operate= '/^.+(\s(as)\s.+)?$/';
    protected $_Foreach_Begin = '/\<foreach\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    protected $_Foreach_End = '/\<[\/]foreach\s*\>/';
    /**
     * 对象数据存储
     * @var array $_Param_Array
    */
    protected $_Param_Array = array();
    /**
     * 构造方法 获取引用页面地址信息
     * @access public
     * @param string $page
     * @param array $param
     */
    function __construct($page, $param)
    {
        parent::__construct($page, $param);
    }
}