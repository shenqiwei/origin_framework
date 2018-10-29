<?php
/**
 * coding: utf-8 *
 * system OS: windows10 *
 * work Tools:Phpstorm *
 * language Ver: php7.3 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Application.Controller *
 * version: 1.0 *
 * structure: common framework *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context: Action控制单元封装
 */
namespace Origin;
# 调用映射描述模板封装
use Origin\Kernel\Transaction\Action as Transaction;
# 调用工厂结构模块
use Origin\Kernel\Transaction\Example\Factory;
# 调用入口结构模块
use Origin\Kernel\Transaction\Example\Pass;
# 行为控制主类，控制器单元出口，抽象结构模型
abstract class Action extends Controller
{
    /**
     * @access protected
     * @var string $_Error_code 错误代码
     */
    protected $_Error_code;
    /**
     * @access protected
     * @var array $_Data_array 数据数组
     */
    protected $_Action_array;
    /**
     * @access protected
     * @var array $_Query_array query语句值干预数组，包含主元素内容Data数据，Where条件内容
     */
    protected $_Query_array;
    /**
     * @access protected
     * @var string $_Query_type
     */
    protected $_Query_type;
    /**
     * @access protected
     * @var string $_PageList page信息列表
     */
    protected $_PageList;
    /**
     * @access protected
     * @var string $_PageNum number信息列表
     */
    protected $_PageNum;
    /**
     * @access protected
     * @var string $_Request_Method 设置请求方式
    */
    protected $_Request_Method;
    # 构造方法
    function __construct()
    {
        parent::__construct();
    }
    /**
     * @access protected
     * @param string $mapping 映射对象名称
     * @param string $object 行为约束映射内容名称
     * @return null
     * @context 初始化行为内容单元，执行过程中会验证映射文件指向，默认调用get_class(第一级key指向) 和 get_function(第二级key指向)，
     *           并进行映射内容装载，参数变量设置的指向在映射文件内容有效状态下优先执行
     */
    protected function initialize($mapping = null, $object = null)
    {
        # 初始化执行变量
        $this->_Error_code = null;
        $this->_Action_array = null;
        $this->_Query_array = null;
        $this->_Query_type = null;
        $this->_Request_Method = "post";
        # action主映射指向，默认与调用控制同名
        $_class = $this->get_class();
        # 判断class对象内容是否为完整引述路径
        if (strpos($_class, "\\")) {
            # 转化为数组对象
            $_class = explode("\\", $_class);
            # 抽取class名称
            $_class = $_class[count($_class) - 1];
        }
        $_mapping = $_class;
        # action执行约束指向，默认与调用方法同名
        $_object = $this->get_function();
        # 判断方法参数状态，是否进行指向修改
        if (!is_null($mapping))
            $_mapping = strtolower(trim($mapping));
        if (!is_null($object))
            $_object = strtolower(trim($object));
        # 调取action映射配置文件内容
        if (indexFiles($_url = Config("ROOT_APPLICATION") . Config("APPLICATION_CONFIG") . "Action.cfg.php")) {
            $_action = include(str_replace("/", SLASH, $_url));
        } else {
            # 异常提示：行为配置文件无效
            try {
                throw new \Exception('Origin Action Controller Error: Config is invalid for action config');
            } catch (\Exception $e) {
                echo($e->getMessage());
                exit();
            }
        }
        # 验证一级映射对象是否存在
        if (key_exists($_mapping, $_action)) {
            $_mapping = $_action[$_mapping];
        } else {
            # 异常提示：行为配置文件无效
            try {
                throw new \Exception('Origin Action Config Error: Master config mapping key[' . $_mapping . '] is invalid');
            } catch (\Exception $e) {
                echo($e->getMessage());
                exit();
            }
        }
        # 验证二级映射对象是否存在，并抽取action行为配置内容
        if (key_exists($_object, $_mapping)) {
            $this->_Action_array = array_change_key_case($_mapping[$_object], CASE_LOWER);
        } else {
            # 异常提示：行为配置文件无效
            try {
                throw new \Exception('Origin Action Config Error: Action config mapping key[' . $_object . '] is invalid');
            } catch (\Exception $e) {
                echo($e->getMessage());
                exit();
            }
        }
        return null;
    }
    /**
     * @access public
     * @param string $mapping 执行映射名称，默认状态下自动获取当前执行对象名称
     * @param array $data 对象数据修改内容值数组
     * @param string $method 请求类型
     * @return boolean
     * @context 获取对象数据，并使用自定义数组（data）替换对象数组中指定元素值
     */
    public function setData($mapping = null, $data = null, $method = "post")
    {
        # 创建返回值变量
        $_receipt = false;
        # 抽取模板对象
        if (key_exists($_mark = Model::ACTION_MODEL_OBJECT_MARK, $this->_Action_array)) {
            # 是否自定义映射指向
            $_model = array_change_key_case(Action($this->_Action_array[$_mark], $mapping), CASE_LOWER);
            # 检查模板对象状态
            if (isset($_model) and is_array($_model)) {
                # 实例化工厂模型
                $_factory = new Factory();
                $_factory->setMethod($method);
                # 判断行为模板执行类型
                if (key_exists($_mark = Model::ACTION_QUERY_MARK, $_model)) {
                    $_model = array_change_key_case(Model($this->_Action_array[$_mark], $mapping), CASE_LOWER);
                    $this->_Query_array["data"] = $_factory->index($_model);
                    if (is_null($_factory->getErrorMsg())) {
                        foreach ($data as $_key => $_value) {
                            if (key_exists($_key, $this->_Query_array['data'])) {
                                $this->_Query_array['data'][$_key] = $_value;
                            }
                        }
                    } else {
                        $this->_Error_code = $_factory->getErrorMsg();
                    }
                } elseif (key_exists($_mark = Model::MAPPING_TABLE_MARK, $_model)) {
                    $_type = "select";
                    if (key_exists($_mark = Model::ACTION_TYPE_MARK, $this->_Action_array))
                        $_type = $this->_Action_array[$_mark];
                    $this->_Query_array["data"] = $_factory->index($_model, $_type);
                    if (is_null($_factory->getErrorMsg())) {
                        foreach ($data as $_key => $_value) {
                            if (key_exists($_key, $this->_Query_array['data'])) {
                                $this->_Query_array['data'][$_key] = $_value;
                            }
                        }
                    } else {
                        $this->_Error_code = $_factory->getErrorMsg();
                    }
                } else {
                    # 异常提示：行为操作模板无效
                    try {
                        throw new \Exception('Origin Action Controller Error: Not found object(query|table) mark');
                    } catch (\Exception $e) {
                        echo($e->getMessage());
                        exit();
                    }
                }
            }
        } else {
            # 异常提示：行为配置文件无效
            try {
                throw new \Exception('Origin Action Controller Error: Not found model object');
            } catch (\Exception $e) {
                echo($e->getMessage());
                exit();
            }
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param array $field 查询结构对象数组
     * @return boolean
     * @context 获取列表查询对象
     */
    public function setField($field)
    {
        # 创建返回值变量
        $_receipt = false;
        if (is_array($field) and !empty($field)) {
            $this->_Query_array['field'] = $field;
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param string|array $model 条件语句执行模板字段参数(带参数变量，的条件语句结构字符串) or 条件结构数组
     * @param string $mapping 执行映射名称，默认状态下自动获取当前执行对象名称
     * @param array|string $where 条件结构参数对象数组
     * @return boolean
     * @context 设置条件内容
     */
    public function setWhere($model, $mapping = null, $where = null)
    {
        # 创建返回值变量
        $_receipt = false;
        # 抽取模板对象
        if (key_exists($_mark = Model::ACTION_MODEL_OBJECT_MARK, $this->_Action_array)) {
            # 是否自定义映射指向
            $_model = array_change_key_case(Model($this->_Action_array[$_mark], $mapping), CASE_LOWER);
            # 抽取时间结构变量
            $_time_format = '/\[\[\^:time\]:[^\[\]]+\]/';
            # 抽取变量内容规则(请求器对象元素项)
            $_var_format = '/\[:[^\[\]]+\]/';
            # 执行模板结构验证
            if (is_array($model) and !empty($model)) {
                foreach ($model as $_key => $_value) {
                    if ($_count = preg_match_all($_time_format, $_value, $_time, PREG_SET_ORDER)) {
                        for ($_i = 0; $_i < $_count; $_i++) {
                            # 转化变量内容
                            $_time_format = str_replace("]", null, str_replace("[[^:time]:", null, $_time[$_i][0]));
                            # 转义信息
                            $model[$_key] = str_replace($_time[$_i], "'" . date($_time_format) . "'", $model[$_key]);
                        }
                    }
                    if ($_count = preg_match_all($_var_format, $_value, $_variable, PREG_SET_ORDER)) {
                        for ($_i = 0; $_i < $_count; $_i++) {
                            # 转存对象内容
                            $_var = $_variable[$_i][0];
                            # 区分变量结构
                            # 执行默认请求器模板内容加载
                            $_var = str_replace('[:', null, str_replace(']', null, $_var));
                            if (is_array($where) and !empty($where) and key_exists($_var, $where)) {
                                $model[$_key] = $where[$_var];
                            } else {
                                if (is_array($_model) and !empty($_model)) {
                                    $_pass = new Pass();
                                    $model[$_key] = $_pass->index($_model, $_var);
                                    if (!is_null($_pass->getErrorMsg())) {
                                        $this->_Error_code = $_pass->getErrorMsg();
                                    }
                                }
                            }
                        }
                    }
                }
                $this->_Query_array['where'] = $model;
            } elseif (!is_null($model) and !empty($model)) {
                # 抽取时间结构变量内容
                if ($_count = preg_match_all($_time_format, $model, $_time, PREG_SET_ORDER)) {
                    # 遍历数据内容
                    for ($_i = 0; $_i < $_count; $_i++) {
                        # 转化变量内容
                        $_time_format = str_replace("]", null, str_replace("[[^:time]:", null, $_time[$_i][0]));
                        # 转义信息
                        $model = str_replace($_time[$_i], "'" . date($_time_format) . "'", $model);
                    }
                }
                # 抽取关系变量内容
                if ($_count = preg_match_all($_var_format, $model, $_variable, PREG_SET_ORDER)) {
                    # 循环遍历比对变量内容
                    for ($_i = 0; $_i < $_count; $_i++) {
                        # 转存对象内容
                        $_var = $_variable[$_i][0];
                        # 区分变量结构
                        # 执行默认请求器模板内容加载
                        $_var = str_replace('[:', null, str_replace(']', null, $_var));
                        if (is_array($where) and !empty($where) and key_exists($_var, $where)) {
                            $_var = $where[$_var];
                        } else {
                            if (is_array($_model) and !empty($_model)) {
                                $_pass = new Pass();
                                $_var = $_pass->index($_model, $_var);
                                if (!is_null($_pass->getErrorMsg())) {
                                    $this->_Error_code = $_pass->getErrorMsg();
                                }
                            }
                        }
                        if (is_integer($_var) or is_float($_var) or is_double($_var)) {
                            $model = str_replace($_variable[$_i][0], $_var, $model);
                        } else {
                            $model = str_replace($_variable[$_i][0], "'" . $_var . "'", $model);
                        }
                        $model = str_replace($_variable[$_i][0], $_var, $model);
                        # 条件运算结构转义
                        foreach (array('/\s+gt\s+/' => '>', '/\s+lt\s+/ ' => '<', '/\s+neq\s+/' => '!=', '/\s+eq\s+/' => '=', '/\s+ge\s+/' => '>=', '/\s+le\s+/' => '<=') as $key => $value) {
                            $model = preg_replace($key, $value, $model);
                        }
                    }
                }
                $this->_Query_array['where'] = $model;
            } else {
                # 异常提示：无效模板
                try {
                    throw new \Exception('Origin Action Controller Error:  model is invalid');
                } catch (\Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
        } else {
            # 异常提示：行为配置文件无效
            try {
                throw new \Exception('Origin Action Controller Error: Not found model object');
            } catch (\Exception $e) {
                echo($e->getMessage());
                exit();
            }
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param string $where_model 条件模板
     * @param string $model 准入模板
     * @return boolean
     */
    public function setPageWhere($where_model,$model=null)
    {
        # 创建返回值变量
        $_receipt = false;
        if(!is_null($where_model) and !empty($where_model)){
            # 抽取变量内容规则(请求器对象元素项)
            $_var_format = '/\[:[^\[\]]+\]/';
            if ($_count = preg_match_all($_var_format, $where_model, $_variable, PREG_SET_ORDER)) {
                $_search = null;
                for ($_i = 0; $_i < $_count; $_i++) {
                    # 转存对象内容
                    $_var = $_variable[$_i][0];
                    # 区分变量结构
                    # 执行默认请求器模板内容加载
                    $_var = str_replace('[:', null, str_replace(']', null, $_var));
                    $_pass = new Pass();
                    $_model = Model($this->_Action_array[Model::ACTION_MODEL_OBJECT_MARK],$model);
                    $_value = $_pass->index($_model,$_var);
                    if(!is_null($this->_Error_code))
                        break;
                    else{
                        $model = preg_replace($_variable[$_i][0], $_value, $where_model);
                        $_search .= "&{$_var}={$_value}";
                    }

                }
                if(is_null($this->_Error_code) or !preg_match_all($_var_format, $where_model, $_variable, PREG_SET_ORDER)){
                    $this->_Action_array["where"] = $where_model;
                    $this->_Action_array["search"] = $_search;
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param array $order 排序结构参数对象数组
     * @return boolean
     * @context 设置排序内容
     */
    public function setOrder($order = null)
    {
        # 创建返回值变量
        $_receipt = false;
        if (is_null($order)) {
            $this->_Query_array["order"] = $order;
            $_receipt = true;
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param array $group 分组结构参数对象数组
     * @return boolean
     * @context 设置排序内容
     */
    public function setGroup($group = null)
    {
        # 创建返回值变量
        $_receipt = false;
        if (is_null($group)) {
            $this->_Query_array["group"] = $group;
            $_receipt = true;
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param int $limit_count 数据显示数量
     * @param int $limit_begin 数据查询其实位置
     * @return boolean
     * @context 设置排序内容
     */
    public function setLimit($limit_begin = 0, $limit_count = null)
    {
        # 创建返回值变量
        $_receipt = false;
        if ($limit_count > 0) {
            $this->_Query_array['limit'] = array("begin" => intval($limit_begin), "length" => intval($limit_count));
            $_receipt = true;
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $key 数组元素键
     * @param string $value 数组元素值
     * @context query语句数据与条件干预函数
     */
    protected function setQuery($key, $value)
    {
        if (is_array($this->_Query_array)) {
            $this->_Query_array[$key] = $value;
        } else {
            $this->_Query_array = array($key => $value);
        }
    }
    /**
     * @access protected
     * @param string $method 请求类型
     * @context 请求类型
    */
    protected function setMethod($method)
    {
        if(in_array(strtolower($method),array("get","post")))
            $this->_Request_Method = strtolower($method);
    }
    /**
     * @access protected
     * @param string $obj 执行模板映射对象名称
     * @param string $pass 准入模板指向
     * @return mixed
     * @context 启动action主方法，执行action行为
     */
    protected function action($obj = null, $pass = null)
    {
        # 创建返回信息变量
        $_receipt = null;
        # 验证配置信息载入状态
        if (!is_null($this->_Action_array)) {
            # 创建数据源指向变量
            $_source = null;
            # 抽取数据源指向内容
            if (key_exists($_mark = Model::ACTION_DATA_SOURCE_MARK, $this->_Action_array))
                $_source = $this->_Action_array[$_mark];
            # 创建行为信息表述变量,默认方式 select
            $_type = "select";
            if (key_exists($_mark = Model::ACTION_TYPE_MARK, $this->_Action_array))
                $_type = strtolower($this->_Action_array[$_mark]);
            # 创建模板指向内容变量
            $_model = null;
            if (key_exists($_mark = Model::ACTION_MODEL_OBJECT_MARK, $this->_Action_array))
                $_model = $this->_Action_array[$_mark];
            if (!is_null($_model)) {
                # 抽调action对象模板
                $_model_array = Action($_model, $obj);
                # 判断抽取信息状态
                if (is_array($_model_array) and !empty($_model_array)) {
                    # 实例化事务类
                    $_transaction = new Transaction();
                    # 设置默认请求器模板
                    $_transaction->setDefault($_model);
                    # 设置默认请求模板指向
                    $_transaction->setPass($pass);
                    # 设置默认数据源地址指向
                    $_transaction->setSource($_source);
                    # 设置数据操作默认执行类型
                    $_transaction->setType($_type);
                    # 选择事务类型
                    if (key_exists(Model::ACTION_QUERY_MARK, $_model_array)) {
                        $_transaction->setMethod($this->_Request_Method);
                        $_re = $_transaction->query($_model_array, $this->_Query_array);
                        if (is_null($_transaction->getErrorMsg())) {
                            $_receipt = $_re;
                        } else {
                            $this->_Error_code = $_transaction->getErrorMsg();
                        }
                    } elseif (key_exists(Model::MAPPING_TABLE_MARK, $_model_array)) {
                        $_transaction->setMethod($this->_Request_Method);
                        $_re = $_transaction->model($_model_array, $this->_Query_array);
                        if (is_null($_transaction->getErrorMsg())) {
                            $_receipt = $_re;
                        } else {
                            $this->_Error_code = $_transaction->getErrorMsg();
                        }
                    } else {
                        # 异常提示：行为操作模板无效
                        try {
                            throw new \Exception('Origin Action Controller Error: Not found object(query|table) mark');
                        } catch (\Exception $e) {
                            echo($e->getMessage());
                            exit();
                        }
                    }
                } else {
                    # 异常提示：行为操作模板无效
                    try {
                        throw new \Exception('Origin Action Controller Error: Action operation model is invalid');
                    } catch (\Exception $e) {
                        echo($e->getMessage());
                        exit();
                    }
                }
            } else {
                # 异常提示：行为配置文件无效
                try {
                    throw new \Exception('Origin Action Controller Error: Not found model object');
                } catch (\Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
        } else {
            # 异常提示：行为配置文件无效
            try {
                throw new \Exception('Origin Action Controller Error: Please,initialize action in the first');
            } catch (\Exception $e) {
                echo($e->getMessage());
                exit();
            }
        }
        # 返回内容
        return $_receipt;
    }
    /**
     * @access protected
     * @param string $obj 执行模板映射对象名称
     * @param string $pass 准入模板指向
     * @return mixed
     * @context 分页模块
     */
    protected function paging($obj = null, $pass = null)
    {
        # 创建返回信息变量
        $_receipt = null;
        # 验证配置信息载入状态
        if (!is_null($this->_Action_array)) {
            # 创建数据源指向变量
            $_source = null;
            # 抽取数据源指向内容
            if (key_exists($_mark = Model::ACTION_DATA_SOURCE_MARK, $this->_Action_array))
                $_source = strtolower($this->_Action_array[$_mark]);
            # 创建模板指向内容变量
            $_model = null;
            if (key_exists($_mark = Model::ACTION_MODEL_OBJECT_MARK, $this->_Action_array))
                $_model = $this->_Action_array[$_mark];
            if (!is_null($_model)) {
                # 抽调action对象模板
                $_model_array = Action($_model, $obj);
                # 判断抽取信息状态
                if (is_array($_model_array) and !empty($_model_array)) {
                    # 实例化事务类
                    $_transaction = new Transaction();
                    # 设置默认请求器模板
                    $_transaction->setDefault($_model);
                    # 设置默认请求模板指向
                    $_transaction->setPass($pass);
                    # 设置默认数据源地址指向
                    $_transaction->setSource($_source);
                    # 选择事务类型
                    if (key_exists(Model::ACTION_QUERY_MARK, $_model_array)) {
                        if (key_exists($_key = Model::ACTION_PAGE_MARK, $_model_array)) {
                            $_page_array = $_model_array[$_key];
                            # 条件信息
                            $_page_where = null;
                            if (!is_null($this->_Query_array) and key_exists("search", $this->_Query_array)) {
                                $_page_where = $this->_Query_array["search"];
                            }
                            # 翻页识别标记
                            $_page_mark = "page";
                            if (key_exists($_key = Model::ACTION_PAGE_CURRENT, $_page_array)) {
                                $_page_mark = strtolower($_page_array[$_key]);
                            }
                            # 当前页码
                            $_page_current = 1;
                            if (intval(Input("GET." . $_page_mark)) >= 1) {
                                $_page_current = intval(Input("GET." . $_page_mark));
                            }
                            $_page_size = 5;
                            if (key_exists($_key = Model::ACTION_PAGE_SIZE, $_page_array)) {
                                if (intval($_num = $_page_array[$_key])) {
                                    $_page_size = $_num;
                                }
                            }
                            $_page_uri = null;
                            if (key_exists($_key = Model::ACTION_PAGE_URI, $_page_array)) {
                                $_page_uri = $_page_array[$_key];
                            }
                            $_page_style = null;
                            if (key_exists($_key = Model::ACTION_PAGE_STYLE, $_page_array)) {
                                $_page_style = $_page_array[$_key];
                            }
                            $_page_number = null;
                            if (key_exists($_key = Model::ACTION_PAGE_NUMBER, $_page_array)) {
                                $_page_number = $_page_array[$_key];
                            }
                            if (key_exists($_key = Model::ACTION_PAGE_QUERY, $_page_array)) {
                                $_query = $_page_array[$_key];
                                $_count = $_transaction->count($_query, $this->_Query_array);
                                $this->_PageList = Page($_page_uri, $_count[0][0], $_page_current, $_page_style, $_page_size, $_page_where);
                                $this->setLimit($this->_PageList['limit'], $this->_PageList['size']);
                                $_re = $_transaction->query($_model_array, $this->_Query_array);
                                $this->_PageNum = Number($this->_PageList, $_page_style, $_page_where, $_page_number);
                                if (is_null($_transaction->getErrorMsg())) {
                                    $_receipt = $_re;
                                } else {
                                    $this->_Error_code = $_transaction->getErrorMsg();
                                }
                            } else {
                                # 异常提示：行为操作模板无效
                                try {
                                    throw new \Exception('Origin Action Controller Error: Not found query(count) mark');
                                } catch (\Exception $e) {
                                    echo($e->getMessage());
                                    exit();
                                }
                            }
                        } else {
                            # 异常提示：行为操作模板无效
                            try {
                                throw new \Exception('Origin Action Controller Error: Not found object(page) mark');
                            } catch (\Exception $e) {
                                echo($e->getMessage());
                                exit();
                            }
                        }
                    } elseif (key_exists(Model::MAPPING_TABLE_MARK, $_model_array)) {
                        if (key_exists($_key = Model::ACTION_PAGE_MARK, $_model_array)) {
                            $_page_array = $_model_array[$_key];
                            # 条件信息
                            $_page_where = null;
                            if (key_exists($_key = Model::ACTION_WHERE_MARK, $this->_Query_array)) {
                                $_page_where = $this->_Query_array[$_key];
                            }
                            # 翻页识别标记
                            $_page_mark = "page";
                            if (key_exists($_key = Model::MAPPING_PAGE_CURRENT, $_page_array)) {
                                $_page_mark = strtolower($_page_array[$_key]);
                            }
                            # 当前页码
                            $_page_current = 1;
                            if (intval(Input("GET." . $_page_mark)) >= 1) {
                                $_page_current = intval(Input("GET." . $_page_mark));
                            }
                            $_page_size = 5;
                            if (key_exists($_key = Model::MAPPING_PAGE_SIZE, $_page_array)) {
                                if (intval($_num = $_page_array[$_key])) {
                                    $_page_size = $_num;
                                }
                            }
                            $_page_uri = null;
                            if (key_exists($_key = Model::MAPPING_PAGE_URI, $_page_array)) {
                                $_page_uri = $_page_array[$_key];
                            }
                            $_page_style = null;
                            if (key_exists($_key = Model::MAPPING_PAGE_STYLE, $_page_array)) {
                                $_page_style = $_page_array[$_key];
                            }
                            $_page_number = null;
                            if (key_exists($_key = Model::MAPPING_PAGE_NUMBER, $_page_array)) {
                                $_page_number = $_page_array[$_key];
                            }
                            $_count = $_transaction->count(array("table"=>$_model_array[Model::MAPPING_TABLE_MARK]), $this->_Query_array);
                            $this->_PageList = Page($_page_uri, $_count, $_page_current, $_page_style, $_page_size, $_page_where);
                            $this->setLimit($this->_PageList['limit'], $this->_PageList['size']);
                            $_re = $_transaction->model($_model_array, $this->_Query_array);
                            $this->_PageNum = Number($this->_PageList, $_page_style, $_page_where, $_page_number);
                            if (is_null($_transaction->getErrorMsg())) {
                                $_receipt = $_re;
                            } else {
                                $this->_Error_code = $_transaction->getErrorMsg();
                            }
                        } else {
                            # 异常提示：行为操作模板无效
                            try {
                                throw new \Exception('Origin Action Controller Error: Not found object(page) mark');
                            } catch (\Exception $e) {
                                echo($e->getMessage());
                                exit();
                            }
                        }
                    } else {
                        # 异常提示：行为操作模板无效
                        try {
                            throw new \Exception('Origin Action Controller Error: Not found object(query|table) mark');
                        } catch (\Exception $e) {
                            echo($e->getMessage());
                            exit();
                        }
                    }
                } else {
                    # 异常提示：行为操作模板无效
                    try {
                        throw new \Exception('Origin Action Controller Error: Action operation model is invalid');
                    } catch (\Exception $e) {
                        echo($e->getMessage());
                        exit();
                    }
                }
            } else {
                # 异常提示：行为配置文件无效
                try {
                    throw new \Exception('Origin Action Controller Error: Not found model object');
                } catch (\Exception $e) {
                    echo($e->getMessage());
                    exit();
                }
            }
        } else {
            # 异常提示：行为配置文件无效
            try {
                throw new \Exception('Origin Action Controller Error: Please,initialize action in the first');
            } catch (\Exception $e) {
                echo($e->getMessage());
                exit();
            }
        }
        # 返回内容
        return $_receipt;
    }

    /**
     * @access public
     * @return mixed
     * @context 返回page信息
     */
    function getPageList()
    {
        return $this->_PageList;
    }
    /**
     * @access public
     * @return mixed
     * @context 返回number信息
     */
    function getPageNum()
    {
        return $this->_PageNum;
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