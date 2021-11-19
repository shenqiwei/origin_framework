<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin队列功能封装
 */
namespace Origin\Package;

class Queue
{
    /**
     * 创建任务队列目录
     * @access public
     * @static
     * @param string $queue 创建队列名称
     * @return boolean 返回执行结果状态值
     */
    static function make(string $queue): bool
    {
        $dir = RESOURCE_PUBLIC."/queue";
        # 创建返回值变量
        $receipt = false;
        if(!file_exists("$dir/$queue")){
            $folder = new Folder($dir);
            $receipt = $folder->create($queue, true);
            if($receipt){
                $file = new File("$dir/$queue");
                $receipt = $file->create("origin_queue.tmp", true);
                $file->write("origin_queue.tmp","w",json_encode(array("list"=>null,"create_time"=>time())));
            }
        }
        return $receipt;
    }

    /**
     * 获取当前队列任务数量
     * @access public
     * @static
     * @param string $queue 队列名称
     * @return int|false 返回查询结果或者失败状态
     */
    static function count(string $queue)
    {
        $dir = RESOURCE_PUBLIC."/queue/$queue";
        if(file_exists($dir)){
           if(is_file("$dir/origin_queue.tmp")){
               $file = new File($dir);
               $string = $file->read("origin_queue.tmp");
               $array = json_decode($string,true);
               return count($array["list"]);
           }else
               return false;
        }else
            return false;
    }

    /**
     * 插入队列
     * @access public
     * @static
     * @param string $queue 队列名称
     * @param array $set 参数集合
     * @return boolean 返回执行结果状态值
     */
    static function push(string $queue, array $set): bool
    {
        $dir = RESOURCE_PUBLIC."/queue/$queue";
        $receipt = false;
        if(file_exists($dir)){
            if(is_file(replace("$dir/origin_queue.tmp"))){
                $file = new File($dir);
                $string = $file->read("origin_queue.tmp");
                $array = json_decode($string,true);
                $string = json_encode($set);
                $files = sha1($string)."tmp";
                if($file->create($files)){
                    if($file->write($files,"w",$string)){
                        array_push($array["list"],array("tmp"=>$files));
                        $file->write("origin_queue.tmp","w",json_encode($array));
                        $receipt = true;
                    }
                }
            }
        }
        return $receipt;
    }

    /**
     * 抽取第一个任务信息
     * @access public
     * @static
     * @param string $queue 队列名称
     * @return array|false 返回抽取对象数组或失败状态
     */
    static function extract(string $queue)
    {
        $dir = RESOURCE_PUBLIC."/queue/{$queue}";
        $receipt = false;
        if(file_exists($dir)){
            if(is_file(replace("{$dir}/origin_queue.tmp"))){
                $file = new File($dir);
                $string = $file->read("origin_queue.tmp");
                $array = json_decode($string,true);
                $set = array_shift($array["list"]);
                $tmp = $set["tmp"];
                if(is_file($queue.DS.$tmp)){
                    $receipt = $file->read($tmp);
                    $file->write("origin_queue.tmp","w",json_encode($array));
                    unlink($queue.DS.$tmp);
                    $receipt = json_decode($receipt,true);
                }
            }
        }
        return $receipt;
    }

    /**
     * @access public
     * @static
     * @param string $queue 队列名称
     * @return boolean 返回执行结果状态值
     * @context 清空队列
     */
    static function clear(string $queue): bool
    {
        $dir = RESOURCE_PUBLIC."/queue";
        $receipt = false;
        if(!file_exists("{$dir}/{$queue}")){
            $folder = new Folder($dir);
            $list = $folder->get($queue);
            if($count = count($list)){
                for($i = 0;$i < $count;$i++){
                    if(is_file($dir."/{$queue}".$list[$i]["folder_uri"]))
                        unlink($dir."/{$queue}".$list[$i]["folder_uri"]);
                }
            }
            $receipt = $folder->remove($queue);
        }
        return $receipt;
    }
}
