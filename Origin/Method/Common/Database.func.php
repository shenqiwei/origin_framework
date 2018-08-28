<?php
/**
 * 默认数据库操作方法
 * @access public
 * @param string $object 操作对象，关系数据库状态下，为表对象，非关系数据库下，为集合对象或操作列表对象，默认值为空
 * @return object
*/
function Dao($object=null)
{
    switch(strtolower(trim(Config('DATA_TYPE')))){
        case 'mongo':
            $_dao = new \Origin\Kernel\Data\Mongo();
            if(!is_null($object)){
                $_dao->set($object);
            }
            break;
        case 'redis':
            $_dao = new \Origin\Kernel\Data\Redis();
            break;
        case 'mysql':
        default:
            $_dao = new \Origin\Kernel\Data\Mysql();
            if(!is_null($object)){
                $_dao->table($object);
            }
            break;
    }
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * Mysql数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
*/
function Mysql($connect_name)
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
function Redis($connect_name)
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
function Mongo($connect_name)
{
    $_dao = new \Origin\Kernel\Data\Mongo($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * MongoDB数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
*/
function Mongodb($connect_name)
{
    $_dao = new \Origin\Kernel\Data\Mongodb($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * @access public
 * @param array $page 分页数组
 * @param array $style 分页样式
 * @param array $search 搜索条件
 * @param string $cols 页码数量
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function Number($page,$style,$search,$cols){
    //执行数字页码
    $n=array();
    if($page['count']>$cols){
        $k=($cols%2==0)?$cols/2:($cols-1)/2;
        if(($page['current']-$k)>1 && ($page['current']+$k)<$page['count']){
            $page['num_begin']=$page['current']-$k;
            $page['num_end']=$page['current']+$k;
            for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                if($i==$page['current']){
                    array_push($n,array('page'=>$i,'class'=>$style['mouse_on'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                }else{
                    array_push($n,array('page'=>$i,'class'=>$style['mouse_off'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                }
            }
        }else{
            if(($page['current']-$k)<=1){
                $page['num_begin']=1;
                $page['num_end']=$cols;
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'class'=>$style['mouse_on'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                    }else{
                        array_push($n,array('page'=>$i,'class'=>$style['mouse_off'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                    }
                }
            }elseif(($page['current']+$k)>=$page['count']){
                $page['num_begin']=$page['count']-($cols-1);
                $page['num_end']=$page['count'];
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'class'=>$style['mouse_on'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                    }else{
                        array_push($n,array('page'=>$i,'class'=>$style['mouse_off'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                    }
                }
            }else{
                $page['num_begin']=1;
                $page['num_end']=$cols;
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'class'=>$style['mouse_on'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                    }else{
                        array_push($n,array('page'=>$i,'class'=>$style['mouse_off'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
                    }
                }
            }
        }
    }else{
        for($i=1;$i<=$page['count'];$i++){
            if($i==$page['current']){
                array_push($n,array('page'=>$i,'class'=>$style['mouse_on'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
            }else{
                array_push($n,array('page'=>$i,'class'=>$style['mouse_off'],'url'=>$page['url'].'?page='.$i.$search['search_page']));
            }
        }
    }
    return $n;
}
/**
 * @access public
 * @param string $url 链接
 * @param string $count 总数
 * @param string $current 当前页数
 * @param array $style 分页样式
 * @param string $row 分页大小
 * @param array $search 搜索条件
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function Page($url,$count,$current,$style,$row,$search){
    $page=array(
        'url'=>$url,
        'size'=>intval($row),'num_begin'=>0,'num_end'=>0,'count'=>0,'limit'=>0,'current'=>1,//翻页基本参数
        'first_class'=>$style['first'],'first_url'=>'','first'=>0,//第一页参数
        'last_class'=>$style['previous'],'last_url'=>'','last'=>0,//上一页参数
        'next_class'=>$style['next'],'next_url'=>'','next'=>0,//下一页参数
        'end_class'=>$style['last'],'end_url'=>'','end'=>0,//最后一页参数
        'num'=>5,//页码翻页参数
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
        $page['first_class'].=$style['mouse_off'];
        $page['last_class'].=$style['mouse_off'];
    }

    //判断翻页状态2
    if($page['current']>=$page['count']){
        $page['next']=$page['count'];
        $page['next_class'].=$style['mouse_off'];
        $page['end_class'].=$style['mouse_off'];
    }else{
        $page['next']=$page['current']+1;
    }
    $page['first_url']=$page['url'].'?page=1'.$search['search_page'];//第一页
    $page['last_url']=$page['url'].'?page='.$page['last'].$search['search_page'];//上一页
    $page['next_url']=$page['url'].'?page='.$page['next'].$search['search_page'];//下一页
    $page['end_url']=$page['url'].'?page='.$page['count'].$search['search_page'];//最后一页
    return $page;
}
/**
 * Verify验证函数
 * @param $width
 * @param $height
 * @return object
*/
function Verify($width=120, $height=50)
{
    return new \Origin\Kernel\Export\Verify($width,$height);
}