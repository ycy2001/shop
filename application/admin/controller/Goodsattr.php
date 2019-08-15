<?php
namespace app\Admin\controller;
use Request;
use think\Db;
class Goodsattr extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function show(){
        $data=Request::post();
        $id=$data['id'];
        if ($id==0){
            $arr=Db::query("select attribute.id as aid,attribute.name as aname,attrcate_id,attr_category.name as attr_category_name FROM `attribute`inner join `attr_category` on attribute.attrcate_id=attr_category.id order by aid asc");
            $json=['code'=>'0','status'=>'ok','data'=>$arr];
            return json($json);
        }else{
            $arr=Db::query("select attribute.id as aid,attribute.name as aname,attrcate_id,attr_category.name as attr_category_name FROM `attribute`inner join `attr_category` on attribute.attrcate_id=attr_category.id where attrcate_id='$id' order by aid asc");
            $json=['code'=>'0','status'=>'ok','data'=>$arr];
            return json($json);
        }

    }
    public function show_attrcate(){
        $arr=DB::query("select * from attr_category order by id asc");
        echo json_encode($arr);
    }
    public function showattrcate(){
        $arr=DB::query("select * from attr_category order by id asc");
        $json = ['code' => '0', 'status' => 'ok', 'data' => $arr];
        echo json_encode($json);
    }
    public function addAction(){
        $data=Request::post();
        $name=$data['attr_name'];
        $attrcate_id=$data['attrcate_id'];
//        $validate = new \app\admin\validate\Permission;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'error','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $arr=Db::query("select * from attribute where name='$name'and attrcate_id='$attrcate_id'");
        if (empty($arr)&&$attrcate_id!=0) {
            $add = Db::query("insert into `attribute` (`name`,`attrcate_id`)values('$name','$attrcate_id')");
            $arr = ['code' => '0', 'status' => 'ok', 'data' => '添加成功'];
            echo json_encode($arr);
        }else{
            $arr = ['code' => '1', 'status' => 'error', 'data' => '添加失败'];
            echo json_encode($arr);
        }
    }
    public function addattr(){
        $data=Request::post();
        $attrcate_name=$data['attrcate_name'];
//        $validate = new \app\admin\validate\Permission;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'error','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $arr=Db::query("select * from attr_category where name='$attrcate_name'");
        if (empty($arr)&&!empty($attrcate_name)) {
            $add = Db::query("insert into `attr_category` (`name`)values('$attrcate_name')");
            $arr = ['code' => '0', 'status' => 'ok', 'data' => '添加成功'];
            echo json_encode($arr);
        }else{
            $arr = ['code' => '1', 'status' => 'error', 'data' => '添加失败'];
            echo json_encode($arr);
        }
    }
    public function detaile_show(){
        $data=Request::post();
        $attr_id=$data['attr_id'];
        $arr=Db::query("select * from attr_detaile where attr_id='$attr_id'order by id asc");
        $json = ['code' => '0', 'status' => 'ok', 'data' => $arr];
        echo json_encode($json);
    }
    public function detaile_add(){
        $data=Request::post();
        $attr_id=$data['attr_id'];
        $name=$data['name'];
//        $validate = new \app\admin\validate\Permission;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'error','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $arr=Db::query("select * from attr_detaile where attr_id='$attr_id'and name='$name'");
        if (empty($arr)) {
            $add = Db::query("insert into `attr_detaile` (`name`,`attr_id`)values('$name','$attr_id')");
            $arr = ['code' => '0', 'status' => 'ok', 'data' => '添加成功'];
            echo json_encode($arr);
        }
    }
}