<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/10/17
 * Time: 15:35
 */

namespace Origin\Kernel\Transaction;

# 调用注册操作封装类
use Origin\Kernel\Transaction\Example\Model;
use Origin\Kernel\Transaction\Example\Query;
use Origin\Kernel\Transaction\Example\Pass;
use Origin\Kernel\Transaction\Example\Factory;
# 模板元素表述封装
use Origin\Model as Mapping;

class Action
{
    /**
     * @access protected
     * @var string $_Default 默认准入指向模板
     */
    protected $_Default = null;
    /**
     * @access protected
     * @var string $_Error_code 错误编码
     */
    protected $_Error_code = null;
    /**
     * @access protected
     * @var string $_Data_source 数据源指向
     */
    protected $_Data_source = null;
    /**
     * @access protected
     * @var string $_Action_type 执行类型
     */
    protected $_Action_type = "select";
    /**
     * @access protected
     * @var array $_Action_data 执行对象
     */
    protected $_Action_data = null;

    /**
     * @access public
     * @param string $source 数据源指向
     * @context 设置数据源指向，用于提示数据执行内核从对应数据配置中抽取数据源对应内容
     */
    public function setSource($source)
    {
        $this->_Data_source = $source;
    }
    /**
     * @access public
     * @param string $default 默认指向
     * @context 设置默认准入模板
     */
    public function setDefault($default)
    {
        $this->_Default = $default;
    }
    /**
     * @access public
     * @param string $type 执行行为类型
     * @context 执行类型限制设置方法
     */
    public function setType($type)
    {
        # 判断执行对象类型是否符合规则标准
        if(in_array(strtolower($type),array("select","insert","update","delete","count")))
            # 设置执行对象
            $this->_Action_type = strtolower($type);
    }
    /**
     * @access public
     * @param array $data 数据对象
     * @context 获取对象数据内容
     */
    public function setData($data)
    {
        # 判断传入数据对象是否有效
        if(is_array($data) and !empty($data))
            # 存储数据对象内容
            $this->_Action_data = $data;
    }
    /**
     * @access public
     * @param array $model 执行映射模板内容
     * @param array $query_data 内容干预数据数组
     * @param string|int $default 默认访问映射地址
     * @return mixed
     * @context query语句执行结构函数，只支持mysql行为操作,控制单元内容，模块
    */
    function query($model,$query_data=null,$default=null)
    {
        # 创建返回值变量
        $_receipt = null;
        # 抽取语句源内容
        if(key_exists($_mark = Mapping::ACTION_QUERY_MARK,$model)){
            # 获取语句源内容
            $_query = $model[$_mark];
            # 抽取变量内容规则(映射对象元素项)
            $_column_format = '/\[:[^\[\]]+(:[^\[\]]+)?:\]/';
            # 抽取关系变量内容
            if($_count = preg_match_all($_column_format,$_query,$_variable,PREG_SET_ORDER)){
;                # 抽取元素列表内容
                if(key_exists($_mark = Mapping::ACTION_COLUMN_MARK,$model)){
                    $_column_list = array_change_key_case($model[$_mark],CASE_LOWER);
                    # 循环遍历比对变量内容
                    for($_i = 0;$_i < $_count;$_i++){
                        # 转存对象内容
                        $_var = $_variable[$_i][0];
                        # 区分变量结构
                        if(preg_match('/^\[:[^\[\]]+:[^\[\]]+:\]$/',$_var)){
                            $_var = str_replace('[:',null,str_replace(':]',null,$_var));
                            $_vars = explode(':',strtolower($_var));
                            if(key_exists($_vars[0],$_column_list)){
                                $_column = $_column_list[$_vars[0]];
                            }
                            $_var = $_vars[1];
                        }else{
                            if(!is_null($default) and !is_numeric($default)){
                                if(key_exists(strtolower(trim($default)),$_column_list)){
                                    $_column = $_column_list[$default];
                                    $_var = str_replace('[:',null,str_replace(':]',null,$_var));
                                }
                            }else{
                                if(key_exists(intval($default),$_column_list)){
                                    $_column = $_column_list[intval($default)];
                                    $_var = str_replace('[:',null,str_replace(':]',null,$_var));
                                }
                            }
                        }
                        if(isset($_column)){
                            if(key_exists($_var,$_column)){
                                $_mysql = new Query($this->_Data_source);
                                if(Mapping::ACTION_LIMIT_MARK === $_var){
                                    if(is_array($_column[$_var])){
                                        $_mysql->limit(intval($_column[$_var][Mapping::ACTION_LIMIT_BEGIN_MARK]),intval($_column[$_var][Mapping::ACTION_LIMIT_LENGTH_MARK]));
                                        $_limit = preg_replace('/^\s*limit\s+/',null,$_mysql->_Limit);
                                        $_query = str_replace($_variable[$_i][0],$_limit,$_query);
                                    }else{
                                        $_query = str_replace($_variable[$_i][0],$_column[$_var],$_query);
                                    }
                                }elseif(Mapping::ACTION_WHERE_MARK === $_var){
                                    if(is_array($_column[$_var])){
                                        $_mysql->where($_column[$_var]);
                                        $_query = str_replace($_variable[$_i][0],$_mysql->_Where,$_query);
                                    }else{
                                        $_query = str_replace($_variable[$_i][0],$_column[$_var],$_query);
                                    }
                                }elseif(Mapping::ACTION_ORDER_MARK === $_var){
                                    if(is_array($_column[$_var])){
                                        $_mysql->Order($_column[$_var]);
                                        $_order = preg_replace('/^\s*order\s+by\s+/',null,$_mysql->_Order);
                                        $_query = str_replace($_variable[$_i][0],$_order,$_query);
                                    }else{
                                        $_query = str_replace($_variable[$_i][0],$_column[$_var],$_query);
                                    }
                                }elseif(Mapping::ACTION_GROUP_MARK === $_var){
                                    if(is_array($_column[$_var])){
                                        $_mysql->Group($_column[$_var]);
                                        $_group = preg_replace('/^\s*group\s+by\s+/',null,$_mysql->_Group);
                                        $_query = str_replace($_variable[$_i][0],$_group,$_query);
                                    }else{
                                        $_query = str_replace($_variable[$_i][0],$_column[$_var],$_query);
                                    }
                                }else{
                                    if(is_array($_column[$_var])){
                                        $_mysql->field($_column[$_var]);
                                        $_query = str_replace($_variable[$_i][0],$_mysql->_Field,$_query);
                                    }else{
                                        $_query = str_replace($_variable[$_i][0],$_column[$_var],$_query);
                                    }
                                }
                            }
                        }
                    }
                    # 抽取变量内容规则(请求器对象元素项)
                    $_var_format = '/\[:[^\[\]]+(:[^\[\]:]+)?\]/';
                    # 抽取关系变量内容
                    if($_count = preg_match_all($_var_format,$_query,$_variable,PREG_SET_ORDER)){
                        # 创建请求器模板内容变量
                        $_cfg = Model($this->_Default);
                        # 循环遍历比对变量内容
                        for($_i = 0;$_i < $_count;$_i++){
                            # 转存对象内容
                            $_var = $_variable[$_i][0];
                            # 区分变量结构
                            if(preg_match('/^\[:[^\[\]]+:[^\[\]:]+\]$/',$_var)){
                                $_var = str_replace('[:',null,str_replace(']',null,$_var));
                                $_vars = explode(':',strtolower($_var));
                                if($_vars[0] === "data" and is_array($query_data['data']) and !empty($query_data['data']) and key_exists($_vars[1],$query_data['data'])){
                                        $_var = $query_data['data'][$_vars[1]];
                                }elseif($_vars[0] === "where" and is_array($query_data['where']) and !empty($query_data['where']) and key_exists($_vars[1],$query_data['where'])){
                                    $_var = $query_data['where'][$_vars[1]];
                                }elseif(is_array($_cfg = Model($_vars[0])) and !empty($_cfg)){
                                    $_pass = new Pass();
                                    $_var = $_pass->index($_cfg,$_vars[1]);
                                    if(!is_null($_pass->getErrorMsg())){
                                        $this->_Error_code = $_pass->getErrorMsg();
                                    }
                                }
                            }else{
                                # 执行默认请求器模板内容加载
                                $_var = str_replace('[:',null,str_replace(']',null,$_var));
                                if(is_array($_cfg) and !empty($_cfg)){
                                    $_pass = new Pass();
                                    $_var = $_pass->index($_cfg,$_var);
                                    if(!is_null($_pass->getErrorMsg())){
                                        $this->_Error_code = $_pass->getErrorMsg();
                                    }
                                }
                            }
                            $_query = str_replace($_variable[$_i][0],$_var,$_query);
                            # 条件运算结构转义
                            foreach(array('/\s+gt\s+/' => '>', '/\s+lt\s+/ ' => '<','/\s+neq\s+/' => '!=', '/\s+eq\s+/'=> '=', '/\s+ge\s+/' => '>=', '/\s+le\s+/' => '<=') as $key => $value){
                                $_query= preg_replace($key, $value, $_query);
                            }
                        }
                    }
                    if(is_null($this->_Error_code)){
                        if(is_null($this->_Data_source)){
                            if(Config("DATA_TYPE") === "mysql"){
                                $_receipt = Dao()->query($_query);
                            }else{
                                $this->_Error_code = "The operated need to use mysql connect";
                            }
                        }else{
                            $_receipt = Mysql($this->_Data_source)->query($_query);
                        }
                    }
                }else{
                    # 异常提示：未设置元素内容
                    $this->_Error_code = "Not found column array";
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param array $model 数据映射对象模板内容
     * @param array $data 数据内容数组
     * @return mixed
     * @context 数据操作，支持mysql，redis，mongodb
    */
    function model($model,$data=null)
    {
        # 创建返回值变量
        $_receipt = null;
        # 抽取表信息
        $_table = $model[Mapping::MAPPING_TABLE_MARK];
        # 抽取主键内容,创建主键对象元素名
        $_major = $_major_column =null;
        if(key_exists($_mark = Mapping::MAPPING_MAJOR_MARK,$model)){
            $_major_config = $model[$_mark];
            if(key_exists($_mark = Mapping::MAPPING_FIELD_OPTION,$_major_config)){
                $_major = $_major_column = $_major_config[$_mark];
            }
            if(key_exists($_mark = Mapping::MAPPING_COLUMN_MARK,$_major_config)){
                $_major_column = $_major_config[$_mark];
            }
        }
        # 抽取成员内容信息
        if(key_exists($_mark = Mapping::MAPPING_COLUMN_MARK,$model)){
            $_column = $model[$_mark];
            if(is_array($_column) and !empty($_column)){
                switch($this->_Action_type){
                    case "insert":
                        if(!is_null($data)){
                        }else{
                            $_factory = new Factory();
                            # 创建请求器模板内容变量
                            $_cfg = Model($this->_Default);
                            # 调用工厂主函数
                            $_data = $_factory->index($_column,$_cfg,'insert');
                            if(!is_null($_factory->getErrorMsg())){
                                $this->_Error_code = $_factory->getErrorMsg();
                            }else{
                                $_M = new Model($this->_Data_source);

                                $_M->insert($_table,$_data);
                            }
                        }
                        break;
                    case "update":
                        if(!is_null($data)){
                        }else{
                            $_factory = new Factory();
                            # 创建请求器模板内容变量
                            $_cfg = Model($this->_Default);
                            # 调用工厂主函数
                            $_data = $_factory->index($_column,$_cfg,'update');
                            if(!is_null($_factory->getErrorMsg())){
                                $this->_Error_code = $_factory->getErrorMsg();
                            }else{
                                $_M = new Model($this->_Data_source);
                                if(key_exists())
                                $_M->update($_table,$_data);
                            }
                        }
                        break;
                    case "delete":
                        if(!is_null($data)){
                        }else{
                        }
                        break;
                    case "count":
                        if(!is_null($data)){
                        }else{
                        }
                        break;
                    default:
                        if(!is_null($data)){
                        }else{
                        }
                        break;
                }
            }
        }else{
            # 异常提示：未设置元素内容
            $this->_Error_code = "Not found column array";
        }
        return $_receipt;
    }
    /**
     * @access public
     * @return string
     * @context 返回错误信息内容
     */
    function getErrorMsg()
    {
        return $this->_Error_code;
    }
}