<?php
namespace app\Admin\controller;
use Request;
use think\Db;
class Productcategory extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function show(){

        $arr=Db::query("select * from category");
        $this->getTree($arr);

    }
    private function getTree($arr,$pid = 0, $level = 0){


        static $list = [];

        echo "<ul>";
        foreach ($arr as $key => $value){


            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['pid'] == $pid){
                // 更新 名称值
                $value['name'] = $value['name'];
                // 输出 名称
                $m_id=$value['id'];
                echo "<li value='$m_id'>".$value['name']."<button onclick='my_delete(".$value['id'].",\"".$value['pid']."\")'>删除</button></li>";
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($arr[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTree($arr, $value['id'], $level+1);

            }

        }
        echo "</ul>";
        return $list;
    }
    public function addaction(){
        $data=Request::post();
//        $validate = new \app\admin\validate\Role;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $pid=$data['pid'];
        $name=$data['name'];
        $getname=Db::query("select * from category where name='$name'");
        if (empty($getname)){
            if ($pid==0){
                $add = Db::query("insert into `category` (`name`,`pid`)values('$name','0')");
                $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
                echo json_encode($arr);
            }else{
                $add = Db::query("insert into `category` (`name`,`pid`)values('$name','$pid')");
                $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
                echo json_encode($arr);
            }
        }else{
            $arr=['code'=>'1','status'=>'error','data'=>'分类不能重复'];
            echo json_encode($arr);
        }
    }
    public function delete(){
        $data=Request::post();
        $id=$data['id'];
        $pid=$data['pid'];
    }
}