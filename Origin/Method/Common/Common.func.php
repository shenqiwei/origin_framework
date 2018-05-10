<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: Zero.Snake.Method.Function *
 * version: 1.0 *
 * structure: common framework *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/04/23 15:26
 * update Time: 2017/04/23 15:26
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 * 应用环境公共文件引入函数
 * @param $guide
 * @return null;
 */
function Common($guide)
{
    $_receipt = null;
    if(strpos($guide,':')){
        $_url = str_replace(SLASH,':',str_replace('/', SLASH, Config('ROOT_APPLICATION'))).$guide;
        $_obj = explode(':', $guide);
        if(strtolower($_obj[0]) === 'config'){
            $_suffix = Config('CONFIG_SUFFIX');
        }elseif(strtolower($_obj[0]) === 'controller' or strtolower($_obj[0]) === 'class'){
            $_suffix = Config('CLASS_SUFFIX');
        }else{
            $_suffix = Config('METHOD_SUFFIX');
        }
        $_receipt = Hook($_url,$_suffix);
    }
    return $_receipt;
}
/**
 * 文件及配置名规则函数
 * @param string $param
 * @return boolean
 */
function Rule($param)
{
    /**
     * @var string $_regular
     * @var boolean $_receipt
     */
    $_regular = '/^[^\_\W\s]+((\:|\\\|\_|\/)?[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match_all($_regular, $param)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * 公共信息处理函数
 * @access public
 * @param $model
 * @param $message
 * @param $url
 * @param $time
 * @return null
 */
function message($model, $message='this is a message',$url='#',$time=5)
{
    $_temp = file_get_contents($model);
    $_temp = str_replace('{$time}', htmlspecialchars(trim($time)), $_temp);
    if (is_array($message)) $message = 'this is a default message';
    $_temp = str_replace('{$message}', htmlspecialchars(trim($message)), $_temp);
    $_temp = str_replace('{$url}', htmlspecialchars(trim($url)), $_temp);
    echo($_temp);
    exit();
}
/**
 * 文件及导向结构规则函数
 * @param string $uri
 * @return boolean
 */
function fileUri($uri)
{
    /**
     * @var string $_regular
     * @var boolean $_receipt
     */
    $_regular = '/^[^\_\W\s]+((\_|\/)?[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match_all($_regular, $uri)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * 文件及导向结构规则函数
 * @param string $uri
 * @return boolean
 */
function formatGuide($uri)
{
    /**
     * @var string $_regular
     * @var boolean $_receipt
     */
    $_regular = '/^[^\_\W\s]+((\_|\:)?[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match_all($_regular, $uri)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @var string $_file_format
 * @return boolean
 * @contact 文件路径格式验证
 */
function formatFile($uri)
{
    /**
     * @var string $_file_format
     * @var boolean $_receipt
     */
    $_file_format = '/^([^\_\W\s]+[\:](\\\|\/))?[^\_\W\s]+((\_|\\\|\/)?[^\_\W\s]+)*(\.[^\_\W\s]+)+$/u';
    $_receipt = false;
    if(preg_match_all($_file_format, $uri)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $name
 * @return boolean
 * @contact 文件名格式验证
 */
function nameFile($name)
{
    /**
     * @var string $_name_format
     * @var boolean $_receipt
     */
    $_name_format = '/^[^\_\W\s]+(\_[^\_\W\s]+)*$/u';
    $_receipt = false;
    if(preg_match_all($_name_format, $name)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $type
 * @return boolean
 * @contact 文件属性结构信息
*/
function formatType($type)
{
    /**
     * @access private
     * @var string $_type_format 文件属性名约束
     */
    $_type_format = '/^(class|func|function|impl|implements|interface|controller|method|common|cfg|config|action|data|file|graph|math|message|info|param|bean|beans|map|mapping|filter|model|view)$/u';
    $_receipt = false;
    if(preg_match_all($_type_format, $type)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $suffix 文件扩展名信息
 * @return boolean
 * @contact 文件宽展名约束
*/
function formatSuffix($suffix)
{
    /**
     * @access private
     * @var string $_auto_type 自定义文件属性约束
     */
    $_auto_suffix = '/^(php|phpx|php5|php7|xhtml|html|htm|log|ini|txt)$/u';
    $_receipt = false;
    if(preg_match_all($_auto_suffix, $suffix)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $mapping
 * @return string
 * @contact 文件结构映射控制
*/
function suffixMap($mapping)
{
    $_mapping = array(
        'controller' => 'class', 'function' => 'func', 'method' => 'func', 'common' => 'func',
        'config' => 'cfg', 'action' => 'act', 'message' => 'info', 'param' => 'bean', 'beans' => 'bean',
        'map' => 'mapping', 'implements' => 'impl', 'interface' => 'impl',
    );
    $_receipt = null;
    if(key_exists($mapping,$_mapping)){
        $_receipt = $_mapping[$mapping];
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $url
 * @return boolean
 * @contact 访问地址结构格式
*/
function urlGuide($url)
{
    $_url_format = '/^((http|https)://)?[^\_\W\s]+((\_|\/)[^\_\W\s]+)*(\.[^\_\W\s\d]+)?(\?[^\_\W\s]+(\_[^\_\W\s]+)*\=[^\&\s]+((\&)[^\_\W\s]+(\_[^\_\W\s]+)*\=[^\&\s]+)*)?$/u';
    $_receipt = false;
    if(preg_match_all($_url_format, $url)){
        $_receipt = true;
    }
    return $_receipt;
}
/**
 * @access public
 * @param string $symbol
 * @return string
 * @contact 比较逻辑运算符双向转化方法
 */
function Symbol($symbol){
    /**
     * 符号替代值：
     * 大于：> - gt - greater than
     * 小于：< - lt - less than
     * 等于：= - eq - equal to
     * 大于等于：>= - ge - greater than and equal to
     * 小于等于：<= - le - less than and equal to
     * @var array $_symbol 符号关系数组
     * @var string $_receipt
     */
    $_receipt = '=';
    $_symbol = array('gt' => '>', 'lt' => '<', 'et'=> '=', 'eq' => '==','neq' => '!=', 'ge' => '>=', 'le' => '<=','heq' => '===', 'nheq' => '!==');
    if(array_key_exists(trim(strtolower($symbol)), $_symbol))
        $_receipt = $_symbol[trim(strtolower($symbol))];
    return $_receipt;
}
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