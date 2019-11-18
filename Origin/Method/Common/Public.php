<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 */
/**
 * Mysql数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
 */
function Mysql($connect_name=null)
{
    /**
     * 调用Mysql数据库核心包
     */
    $_dao = new \Origin\Kernel\Data\Mysql($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * Redis数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
 */
function Redis($connect_name=null)
{
    /**
     * 调用Redis数据库核心包
     */
    $_dao = new \Origin\Kernel\Data\Redis($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * MongoDB数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
 */
function Mongodb($connect_name=null)
{
    $_dao = new \Origin\Kernel\Data\Mongodb($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * Input表单提交信息请求方法函数
 * @access public
 * @param string $key
 * @param mixed $default
 * @return string
 */
function Input($key, $default = null)
{
    # 直接调用Request请求器函数
    return Request($key, $default);
}
/**
 * @access public
 * @param array $page 分页数组
 * @param string $search 搜索条件
 * @param string $cols 页码数量
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function Number($page,$search,$cols){
    //执行数字页码
    $n=array();
    if($page['count']>$cols){
        $k=($cols%2==0)?$cols/2:($cols-1)/2;
        if(($page['current']-$k)>1 && ($page['current']+$k)<$page['count']){
            $page['num_begin']=$page['current']-$k;
            $page['num_end']=$page['current']+$k;
            for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                if($i==$page['current']){
                    array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                }else{
                    array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                }
            }
        }else{
            if(($page['current']-$k)<=1){
                $page['num_begin']=1;
                $page['num_end']=$cols;
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }else{
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }
                }
            }elseif(($page['current']+$k)>=$page['count']){
                $page['num_begin']=$page['count']-($cols-1);
                $page['num_end']=$page['count'];
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }else{
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }
                }
            }else{
                $page['num_begin']=1;
                $page['num_end']=$cols;
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }else{
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }
                }
            }
        }
    }else{
        for($i=1;$i<=$page['count'];$i++){
            if($i==$page['current']){
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
            }else{
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
            }
        }
    }
    return $n;
}
/**
 * @access public
 * @param string $url 链接
 * @param string $count 总数
 * @param string $current 当前页
 * @param string $row 分页大小
 * @param string $search 搜索条件
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function Page($url,$count,$current,$row,$search){
    $page=array(
        'url'=>$url,
        'size'=>intval($row),'num_begin'=>0,'num_end'=>0,'count'=>0,'limit'=>0,'current'=>1,//翻页基本参数
        'first_url'=>'','first'=>0,//第一页参数
        'last_url'=>'','last'=>0,//上一页参数
        'next_url'=>'','next'=>0,//下一页参数
        'end_url'=>'','end'=>0,//最后一页参数
    );
    $page['current']=intval($current);
    $page['count']=$count%$page['size']!=0?intval(($count/$page['size'])+1):intval($count/$page['size']);
    //判断页标状态
    if($page['current']<=0) $page['current']=1;
    if($page['current']>$page['count']) $page['current']=$page['count'];
    if($page['count']<=0) $page['current']=$page['count']=1;
    $page['limit']=$page['size']*($page['current']-1);//其实点运算
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
    $page['first_url']=$page['url'].'?page=1'.$search;//第一页
    $page['last_url']=$page['url'].'?page='.$page['last'].$search;//上一页
    $page['next_url']=$page['url'].'?page='.$page['next'].$search;//下一页
    $page['end_url']=$page['url'].'?page='.$page['count'].$search;//最后一页
    return $page;
}

/**
 * Verify验证函数
 * @param $width
 * @param $height
 * @return object
 */
function Verify($width = 120, $height = 50)
{
    return new \Origin\Kernel\Export\Verify($width, $height);
}