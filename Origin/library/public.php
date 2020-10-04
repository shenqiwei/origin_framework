<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 */
/**
 * Input表单提交信息请求方法函数
 * @access public
 * @param string $key
 * @param mixed $default
 * @return string
 */
function input($key, $default = null)
{
    # 直接调用Request请求器函数
    return request($key, $default);
}
/**
 * @access public
 * @param string $url 链接
 * @param int $count 总数
 * @param int $current 当前页
 * @param int $row 分页大小
 * @param string $search 搜索条件
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function page($url,$count,$current=1,$row=10,$search=null){
    $page=array(
        # 基本参数
        'url'=>$url, # 连接地址
        'count'=>0, # 总数
        'current'=>1, # 当前页码
        'begin'=>0, # 当前列表起始位置
        'limit'=>intval($row), # 显示数量
        'page_begin' => ($current-1)*intval($row)+1,
        'page_count' => $current*intval($row),
        'search' => $search, # 搜索内容
        # 连接参数
        'first_url'=>'','first'=>0, # 第一页参数
        'last_url'=>'','last'=>0, # 上一页参数
        'next_url'=>'','next'=>0, # 下一页参数
        'end_url'=>'','end'=>0, # 最后一页参数
        # number结构参数
        'num_begin'=>0, # Number区间显示翻页页码起始位置
        'num_end'=>0, # Number区间显示翻页页码结束位置
    );
    $page['current']=intval($current);
    $page['count']=$count%$page['limit']!=0?intval(($count/$page['limit'])+1):intval($count/$page['limit']);
    //判断页标状态
    if($page['current']<=0) $page['current']=1;
    if($page['current']>$page['count']) $page['current']=$page['count'];
    if($page['count']<=0) $page['current']=$page['count']=1;
    $page['begin']=$page['limit']*($page['current']-1);//其实点运算
    $page['page_one']=$page['limit']+1;
    $page['page_end']=($page['limit']+$page['size'])>$count?$count:$page['limit']+$page['size'];
    //判断翻页状态1
    if($page['current']>1){
        $page['last']=$page['current']-1;
    }else{
        $page['last']=1;
    }

    //判断翻页状态2
    if($page['current']>=$page['count']){
        $page['next']=$page['count'];
    }else{
        $page['next']=$page['current']+1;
    }
    $page['first_url']=$page['url'].'?page=1'.$page["search"];//第一页
    $page['last_url']=$page['url'].'?page='.$page['last'].$page["search"];//上一页
    $page['next_url']=$page['url'].'?page='.$page['next'].$page["search"];//下一页
    $page['end_url']=$page['url'].'?page='.$page['count'].$page["search"];//最后一页
    return $page;
}
/**
 * @access public
 * @param array $page 分页数组
 * @param int $cols 页码数量
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function number($page,$cols=5){
    //执行数字页码
    $n=array();
    if($page['count']>$cols){
        $k=($cols%2==0)?$cols/2:($cols-1)/2;
        if(($page['current']-$k)>1 && ($page['current']+$k)<$page['count']){
            $page['num_begin']=$page['current']-$k;
            $page['num_end']=$page['current']+$k;
        }else{
            if(($page['current']+$k)>=$page['count']){
                $page['num_begin']=$page['count']-($cols-1);
                $page['num_end']=$page['count'];
            }else{
                $page['num_begin']=1;
                $page['num_end']=$cols;
            }
        }
        for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
            if($i==$page['current']){
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
            }else{
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
            }
        }
    }else{
        for($i=1;$i<=$page['count'];$i++){
            if($i==$page['current']){
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
            }else{
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
            }
        }
    }
    return $n;
}
/**
 * Verify验证函数
 * @param int $width
 * @param int $height
 * @return object
 */
function verify($width = 120, $height = 50)
{
    return new Origin\Package\Verify($width, $height);
}
# 设置异常捕捉回调函数
register_shutdown_function("danger");
/**
 * @access public
 * @return array
 * @context 危险异常捕捉函数
 */
function danger()
{
    $_error = error_get_last();
    define("E_FATAL",  E_ERROR | E_USER_ERROR |  E_CORE_ERROR |
        E_COMPILE_ERROR | E_RECOVERABLE_ERROR| E_PARSE );
    if($_error && ($_error["type"]===($_error["type"] & E_FATAL))) {
        if(DEBUG){
            base($_error);
        }
    }
    return null;
}