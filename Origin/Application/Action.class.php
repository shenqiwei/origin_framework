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
# 继承Origin主控制器
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
     * @var mixed $_Result 返回结果
     */
    protected $_Result;
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
    protected function initialize($mapping=null,$object=null)
    {
        # 初始化执行变量
        $this->_Error_code = null;
        $this->_Action_array = null;
        $this->_Query_array = null;
        $this->_Result = null;
        # action主映射指向，默认与调用控制同名
        $_class = $this->get_class();
        # 判断class对象内容是否为完整引述路径
        if(strpos($_class,"\\")){
            # 转化为数组对象
            $_class = explode("\\",$_class);
            # 抽取class名称
            $_class = $_class[count($_class)-1];
        }
        $_mapping = $_class;
        # action执行约束指向，默认与调用方法同名
        $_object = $this->get_function();
        # 判断方法参数状态，是否进行指向修改
        if(!is_null($mapping))
            $_mapping = strtolower(trim($mapping));
        if(!is_null($object))
            $_object = strtolower(trim($object));
        # 调取action映射配置文件内容
        if(indexFiles($_url = Config("ROOT_APPLICATION").Config("APPLICATION_CONFIG")."Action.cfg.php")){
            $_action = include(str_replace("/",SLASH,$_url));
        }else{
            # 异常提示：行为配置文件无效
            try{
                throw new \Exception('Origin Action Controller Error: Config is invalid for action config');
            }catch(\Exception $e){
                echo($e->getMessage());
                exit();
            }
        }
        # 验证一级映射对象是否存在
        if(key_exists($_mapping,$_action)){
            $_mapping = $_action[$_mapping];
        }else{
            # 异常提示：行为配置文件无效
            try{
                throw new \Exception('Origin Action Config Error: Master config mapping key is invalid');
            }catch(\Exception $e){
                echo($e->getMessage());
                exit();
            }
        }
        # 验证二级映射对象是否存在，并抽取action行为配置内容
        if(key_exists($_object,$_mapping)){
            $this->_Action_array = $_mapping[$_object];
        }else{
            # 异常提示：行为配置文件无效
            try{
                throw new \Exception('Origin Action Config Error: Action config mapping key is invalid');
            }catch(\Exception $e){
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
     * @return boolean
     * @context 获取对象数据，并使用自定义数组（data）替换对象数组中指定元素值
     */
    public function setData($mapping=null,$data=null)
    {
        # 创建返回值变量
        $_receipt = false;
        # 获取数据，并进行标记
        if(is_null($mapping)){
            $_data = input($this->_Action_array[Model::ACTION_MODEL_OBJECT_MARK]);
        }else{
            $_data = input($mapping);
        }
        # 判断是否有需要修改数据
        if(!is_null($data)){
            # 遍历数据
            foreach($data as $_key => $_value){
                # 判断元素是否已存在，如果存在替换数据内容
                if(key_exists($_key,$_data)){
                    $_data[$_key] = $_data;
                }else{
                    # 如果没有相应数据，增加内容数据
                    array_push($_data,array($_key,$_value));
                }
            }
        }
        if(is_array($_data) and !empty($_data)){
            # 值装载并进行标记
            $this->_Query_array["data"] = $_data;
            # 修改状态
            $_receipt = true;
        }
        End:
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
        if(is_array($field) and !empty($field)){
            $this->_Query_array['field'] = $field;
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param array|string $where 条件结构参数对象数组
     * @param string $mapping 执行映射名称，默认状态下自动获取当前执行对象名称
     * @return boolean
     * @context 设置条件内容
     */
    public function setWhere($where=null,$mapping=null)
    {
        # 创建返回值变量
        $_receipt = false;
        # 获取数据，并进行标记
        if(is_null($mapping)){
            $_data = input($this->_Action_array[Model::ACTION_MODEL_OBJECT_MARK]);
        }else{
            $_data = input($mapping);
        }
        # 判断是否预设条件内容
        if(!is_null($where)){
            if(is_array($where)){
                if(!empty($where)){
                    foreach($where as $_key => $_value){
                        if(!is_null($this->_Error_code))
                            break;
                        if(preg_match_all("/\[:[^:\[\]]+\]/",$_value,$_variable,PREG_SET_ORDER)){
                            # 拆分变量结构
                            $_key = str_replace("]",null,str_replace("[:",null,$_variable[0][0]));
                            # 比对变量信息
                            if(key_exists($_key,$_data)){
                                # 执行信息替换
                                $where[$_key] = $_data[$_key];
                            }else{
                                # 条件变量未解析
                                $this->_Error_code = "";
                            }
                        }
                    }
                }
            }else{
                if($_count = preg_match_all("/\[:[^:\[\]]+\]/",$where,$_where,PREG_SET_ORDER)){
                    # 比例条件内容中预设变量
                    for($_i = 0;$_i < $_count;$_i++){
                        # 拆分变量结构
                        $_key = str_replace("]",null,str_replace("[:",null,$_where[$_i][0]));
                        # 比对变量信息
                        if(key_exists($_key,$_data)){
                            # 执行信息替换
                            $where = str_replace($_where[$_i][0],$_data[$_key],$where);
                        }
                    }
                }
            }
        }
        if(is_null($this->_Error_code)){
            # 值装载并进行标记
            $this->_Query_array["where"] = $where;
            # 修改状态
            $_receipt = true;
        }
        End:
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param array $order 排序结构参数对象数组
     * @return boolean
     * @context 设置排序内容
     */
    public function setOrder($order=null)
    {
        # 创建返回值变量
        $_receipt = false;
        if(is_null($order)){
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
    public function setGroup($group=null)
    {
        # 创建返回值变量
        $_receipt = false;
        if(is_null($group)){
            $this->_Query_array["group"] = $group;
            $_receipt = true;
        }
        # 返回状态信息
        return $_receipt;
    }
    /**
     * @access public
     * @param int $limit_begin 数据查询其实位置
     * @param int $limit_count 数据显示数量
     * @return boolean
     * @context 设置排序内容
     */
    public function setLimit($limit_count=0,$limit_begin=null)
    {
        # 创建返回值变量
        $_receipt = false;
        if($limit_count > 0){
            if(is_null($limit_begin)){
                $this->_Query_array["limit"] = intval($limit_count);
            }else{
                $this->_Query_array['limit'] = array("limit_begin"=>intval($limit_begin),"limit_count"=>intval($limit_count));
            }
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
    protected function setQuery($key,$value)
    {
        if(is_array($this->_Query_array)){
            $this->_Query_array[$key] = $value;
        }else{
            $this->_Query_array = array($key=>$value);
        }

    }
    /**
     * @access protected
     * @param string $obj 执行模板映射对象名称
     * @param string $pass 准入模板指向
     * @return mixed
     * @context 启动action主方法，执行action行为
    */
    protected function action($obj=null,$pass=null)
    {
        # 创建返回信息变量
        $_receipt = null;
        # 验证配置信息载入状态
        if(!is_null($this->_Action_array)){
            # 创建数据源指向变量
            $_source = null;
            # 抽取数据源指向内容
            if(key_exists($_mark = Model::ACTION_DATA_SOURCE_MARK,$this->_Action_array))
                $_source = $this->_Action_array[$_mark];
            # 创建行为信息表述变量,默认方式 select
            $_type = "select";
            if(key_exists($_mark = Model::ACTION_TYPE_MARK,$this->_Action_array))
                $_type = $this->_Action_array[$_mark];
            # 创建模板指向内容变量
            $_model = null;
            if(key_exists($_mark = Model::ACTION_MODEL_OBJECT_MARK,$this->_Action_array))
                $_model = $this->_Action_array[$_mark];
            if(!is_null($_model)){
                # 抽调action对象模板
                $_model_array = Action($_model,$obj);
                # 判断抽取信息状态
                if(is_array($_model_array) and !empty($_model_array)){
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
                    if(key_exists(Model::ACTION_QUERY_MARK,$_model_array)){
                        $_re = $_transaction->query($_model_array,$this->_Query_array);
                        if(is_null($_transaction->getErrorMsg())){
                            $_receipt = $_re;
                        }else{
                            $this->_Error_code = $_transaction->getErrorMsg();
                        }
                    }elseif(key_exists(Model::MAPPING_TABLE_MARK,$_model_array)){
                        $_re = $_transaction->model($_model_array,$this->_Query_array);
                        if(is_null($_transaction->getErrorMsg())){
                            $_receipt = $_re;
                        }else{
                            $this->_Error_code = $_transaction->getErrorMsg();
                        }
                    }else{
                        # 异常提示：行为操作模板无效
                        try{
                            throw new \Exception('Origin Action Controller Error: Not found object(query|table) mark');
                        }catch(\Exception $e){
                            echo($e->getMessage());
                            exit();
                        }
                    }
                }else{
                    # 异常提示：行为操作模板无效
                    try{
                        throw new \Exception('Origin Action Controller Error: Action operation model is invalid');
                    }catch(\Exception $e){
                        echo($e->getMessage());
                        exit();
                    }
                }
            }
        }else{
            # 异常提示：行为配置文件无效
            try{
                throw new \Exception('Origin Action Controller Error: Please,initialize action in the first');
            }catch(\Exception $e){
                echo($e->getMessage());
                exit();
            }
        }
        # 返回内容
        return $_receipt;
    }
    /**
     * @access protected
     * @context 分页模块
    */
    protected function paging()
    {}
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