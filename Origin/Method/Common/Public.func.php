<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Function.Method.Public *
 * version: 0.1*
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context:
 * IoC 单字母函数包
 */
/**
 * Action：行为指向及行为控制方法函数
*/
function A()
{
}
/**
 * Backtrack行踪回溯方法
*/
function B()
{}
/**
 * Config公共配置信息调用方法,优先调用用户配置文件，在用户配置文件不存在或者无配置项时，调用系统配置文件
 * @access public
 * @param string $guide
 * @return null
*/
function C($guide)
{
    /**
     * 执行结构为两种，一种是直接调用公共配置文件或者主配置文件，另一种是筛查配置信息位置，在读取配置信息
     * 筛查配置信息，会先检索公共配置信息，然后是主配置信息，最后检索自定义配置文件.
     * 公共配置文件及主配置文件始于框架本体共同存在，所以只在最后检索自定义配置文件是，才会报错异常错误.
     * @var string $_receipt
     * @var array $_config
     * @var string $_regular
     * @var array $_guide
     * @var int $i
    */
    # 创建返回值变量
    $_receipt = null;
    # 创建配置信息初始变量
    $_config = null;
    # 创建引导信息验证正则表达式变量
    $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
    # 验证指引结构信息
    if(is_true($_regular, $guide) === true){
        # 判断是否存在预设连接符号
        if(strpos($guide, ':')){
            # 拆分数组
            $_guide = explode(':', $guide);
            # 引入自定义配置文件
            $_config = J('Config:'.$_guide[0], 'disabled');
            # 判断有无返回信息数组
            if(!$_config){
                for($i=1;$i<count($_guide);$i++){
                    if(is_array($_guide[$i])){
                        $_guide = $_guide[$i];
                    }else{
                        $_receipt = $_guide[$i];
                        break;
                    }
                }
            }
            if($_receipt == null){
                # 使用钩子函数调用公共配置文件
                $_config = J('Config:Config', 'disabled');
                # 判断返回信息
                if($_config){
                    # 引导信息数组，并对数据内容进行匹配
                    for($i=0;$i<count($_guide);$i++){
                        if(is_array($_config[$_guide[$i]])){
                            $_guide = $_config[$_guide[$i]];
                        }else{
                            $_receipt = $_config[$_guide[$i]];
                            break;
                        }
                    }
                }
            }
            if($_receipt == null){
                # 调取公共配置信息
                $_config = Common('Config:Config');
                # 判断返回信息
                if($_config){
                    # 引导信息数组，并对数据内容进行匹配
                    for($i=0;$i<count($_guide);$i++){
                        if(is_array($_config[$_guide[$i]])){
                            $_guide = $_config[$_guide[$i]];
                        }else{
                            $_receipt = $_config[$_guide[$i]];
                            break;
                        }
                    }
                }
            }
            if($_receipt == null){
                # 引导信息数组，并对数据内容进行匹配
                for($i=0;$i<count($_guide);$i++){
                    if(is_array($_config[$_guide[$i]])){
                        $_guide = $_config[$_guide[$i]];
                    }else{
                        $_receipt = $_config[$_guide[$i]];
                        break;
                    }
                }
            }
        }else{
            # 调用默认配置文件信息
            $_config = J('Config:Config', 'disabled');
            if($_config[$guide]){
               $_receipt = $_config[$guide];
            }else{
                # 调取公共配置信息
                $_config = Common('Config:Config');
                if($_config[$guide]){
                    $_receipt = $_config[$guide];
                }else{
                    # 调取主配置信息
                    $_receipt = Config($guide);
                }
            }
        }
    }
    return $_receipt;
}
/**
 * Database数据库操作方法
 * @access public
 * @param string $table
 * @return object
*/
function D($table=null)
{
    /**
     * 调用数据库核心包
    */
    $_source = '\Origin\Kernel\Data\\'.C('DATA_TYPE');
    $d = new $_source();
    if($table != null){
        $d->table($table);
    }
    $d->__setSQL($d);
    return $d;
}
/**
 * Exception异常处理方法
*/
function E()
{}
/**
 * Filter过滤器执行方法
*/
function F()
{}
/**
 *
*/
function G()
{}
/**
 * header文件头结构使用函数
 * @access public
 * @param string $guide
 * @return mixed
*/
function H($guide)
{
    return $guide;
}
/**
 * Input表单提交信息请求方法函数
 * @access public
 * @param string $key
 * @param mixed $default
 * @return string
*/
function I($key, $default=null)
{
    # 直接调用Request请求器函数
    return Request($key, $default);
}
/**
 * fishhook(鱼钩)钩子调用插件及公共组件方法，也可以用于调用新增公共控制器文件，或者函数包
 * @access public
 * @param $guide
 * @param $throws
 * @return mixed
 */
function J($guide, $throws='enable')
{
    /**
     * 使用正则表达式对文件引导信息进行过滤
     * @var mixed $_receipt
     * @var string $_regular
     * @var string $_exception
     * @var mixed $_guide
     */
    $_receipt = null;
    # 创建引导信息验证正则表达式变量
    $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
    # 创建特例变量
    $_exception = null;
    # 验证引导信息是否符合要求
    if(is_true($_regular, $guide) === true){
        # 判断是否存在连接符号
        if(strpos($guide, ':')){
            # 拆分为数组结构
            $_guide = explode(':', $guide);
            # 创建默认扩展名
            $_suffix = Config('CLASS_SUFFIX');
            # 拼接参数信息，并判断是否存在于配置文件中
            if(Config('APPLICATION_'.strtoupper($_guide[0])) and Config(strtoupper($_guide[0]).'_SUFFIX')){
                $_suffix = Config(strtoupper($_guide[0]).'_SUFFIX');
                $_guide[0] = str_replace('/','',Config('APPLICATION_'.strtoupper($_guide[0])));
            }
            $guide= implode(':',$_guide);
            # 判断地址栏中路径信息是否不为空
            if($_SERVER['PATH_INFO']) $_map = explode('/', $_SERVER['PATH_INFO'])[1];
            # 判断函数处理变量是否被创建
            if(isset($_map))
                # 判断获取值与默认应用文件名是否相同
                if($_map != __APPLICATION__)
                    # 判断该值是否问应用目录
                    if(is_dir(ROOT.SLASH.Config('ROOT_APPLICATION').$_map))
                        $_master = $_map.'/';
            # 根据执行结构获取文件路径指向信息
            $_dir = isset($_master) ? $_master:__APPLICATION__;
            # 使用钩子公共方法引入文件
            $_receipt = Hook(str_replace('/',':',Config('ROOT_APPLICATION').$_dir.$guide), $_suffix, $throws);
        }
    }
    return $_receipt;
}
/**
 *
*/
function K()
{}
/**
 * Location 位置跳转方法函数
*/
function L()
{}
/**
 * Mapping数据映射执行方法
*/
function M()
{}
/**
 * @access public
 * @param array $p 分页数组
 * @param array $a 分页样式
 * @param array $f 搜索条件
 * @param string $s 页码数量
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function N($p,$a,$f,$s){
    //执行数字页码
    $n=array();
    if($p['count']>$s){
        $k=($s%2==0)?$s/2:($s-1)/2;
        if(($p['current']-$k)>1 && ($p['current']+$k)<$p['count']){
            $p['num_begin']=$p['current']-$k;
            $p['num_end']=$p['current']+$k;
            for($i=$p['num_begin'];$i<=$p['num_end'];$i++){
                if($i==$p['current']){
                    array_push($n,array('page'=>$i,'class'=>$a['mouse_on'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                }else{
                    array_push($n,array('page'=>$i,'class'=>$a['mouse_off'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                }
            }
        }else{
            if(($p['current']-$k)<=1){
                $p['num_begin']=1;
                $p['num_end']=$s;
                for($i=$p['num_begin'];$i<=$p['num_end'];$i++){
                    if($i==$p['current']){
                        array_push($n,array('page'=>$i,'class'=>$a['mouse_on'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                    }else{
                        array_push($n,array('page'=>$i,'class'=>$a['mouse_off'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                    }
                }
            }elseif(($p['current']+$k)>=$p['count']){
                $p['num_begin']=$p['count']-($s-1);
                $p['num_end']=$p['count'];
                for($i=$p['num_begin'];$i<=$p['num_end'];$i++){
                    if($i==$p['current']){
                        array_push($n,array('page'=>$i,'class'=>$a['mouse_on'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                    }else{
                        array_push($n,array('page'=>$i,'class'=>$a['mouse_off'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                    }
                }
            }else{
                $p['num_begin']=1;
                $p['num_end']=$s;
                for($i=$p['num_begin'];$i<=$p['num_end'];$i++){
                    if($i==$p['current']){
                        array_push($n,array('page'=>$i,'class'=>$a['mouse_on'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                    }else{
                        array_push($n,array('page'=>$i,'class'=>$a['mouse_off'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
                    }
                }
            }
        }
    }else{
        for($i=1;$i<=$p['count'];$i++){
            if($i==$p['current']){
                array_push($n,array('page'=>$i,'class'=>$a['mouse_on'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
            }else{
                array_push($n,array('page'=>$i,'class'=>$a['mouse_off'],'url'=>$p['url'].'?page='.$i.$f['search_page']));
            }
        }
    }
    return $n;
}
/**
 *
*/
function O()
{}
/**
 * @access public
 * @param string $u 链接
 * @param string $c 总数
 * @param string $t 当前页数
 * @param array $a 分页样式
 * @param string $s 分页大小
 * @param array $f 搜索条件
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function P($u,$c,$t,$a,$s,$f){
    $p=array(
        'url'=>$u,
        'size'=>intval($s),'num_begin'=>0,'num_end'=>0,'count'=>0,'limit'=>0,'current'=>1,//翻页基本参数
        'first_class'=>$a['first'],'first_url'=>'','first'=>0,//第一页参数
        'last_class'=>$a['previous'],'last_url'=>'','last'=>0,//上一页参数
        'next_class'=>$a['next'],'next_url'=>'','next'=>0,//下一页参数
        'end_class'=>$a['last'],'end_url'=>'','end'=>0,//最后一页参数
        'num'=>5,//页码翻页参数
    );
    $p['current']=intval($t);
    $p['count']=$c%$p['size']!=0?intval(($c/$p['size'])+1):intval($c/$p['size']);
    //判断页标状态
    if($p['current']<=0) $p['current']=1;
    if($p['current']>$p['count']) $p['current']=$p['count'];
    if($p['count']<=0) $p['current']=$p['count']=1;
    $p['limit']=$p['size']*($p['current']-1);//其实点运算
    $p['page_one']=$p['limit']+1;
    $p['page_end']=($p['limit']+$p['size'])>$c?$c:$p['limit']+$p['size'];
    //判断翻页状态1
    if($p['current']>1){
        $p['last']=$p['current']-1;
    }else{
        $p['last']=1;
        $p['first_class'].=$a['mouse_off'];
        $p['last_class'].=$a['mouse_off'];
    }

    //判断翻页状态2
    if($p['current']>=$p['count']){
        $p['next']=$p['count'];
        $p['next_class'].=$a['mouse_off'];
        $p['end_class'].=$a['mouse_off'];
    }else{
        $p['next']=$p['current']+1;
    }
    $p['first_url']=$p['url'].'?page=1'.$f['search_page'];//第一页
    $p['last_url']=$p['url'].'?page='.$p['last'].$f['search_page'];//上一页
    $p['next_url']=$p['url'].'?page='.$p['next'].$f['search_page'];//下一页
    $p['end_url']=$p['url'].'?page='.$p['count'].$f['search_page'];//最后一页
    return $p;
}
/**
 * 预设Query语句控制单元执行方法，与Model公共控制架构结合使用
*/
function Q()
{}
/**
 * Route路由执行操作方法函数
 * @param string $uri
 * @param boolean $head
 * @return string
*/
function R($uri=null,$head=false)
{
    return null;
}
/**
 *
*/
function S()
{}
/**
 * Template模板执行方法函数
*/
function T()
{}
/**
 * Url连接地址自动装载方法函数
*/
function U()
{
}
/**
 * Verify验证函数
 * @param $width
 * @param $height
 * @return object
*/
function V($width=120, $height=50)
{
    return new \Origin\Kernel\Export\Verify($width,$height);
}
/**
 *
*/
function W()
{}
/**
 *
*/
function X()
{}
/**
 *
 */
function Y()
{}
/**
 *
*/
function Z()
{}