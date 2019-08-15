<?php
namespace app\Admin\controller;
use think\facade\Session;
use Request;
use think\Db;
use gmars\rbac\Rbac;
class Role extends Common
{
    public function list()
    {
        $token=uniqid();
        Session::set('token',$token);
        $this->assign('token',$token);
        return $this->fetch();
    }
    public function add(){
        return $this->fetch();
    }
    public function addAction(){
        $data=Request::post();
        $validate = new \app\admin\validate\Role;
        if (!$validate->check($data)) {
            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        $rbac= new Rbac;
        $name=$data['name'];
        $description=$data['description'];
        $permission_id=$data['permission_id'];
        $getname=Db::query("select * from role where name='$name'");
        $arr=explode(',',$permission_id);
        array_shift($arr);
        $permission_id=implode(',',$arr);
        if (empty($getname)){
            $rbac->createRole([
                'name' => $name,
                'description' => $description,
                'status' => 1
            ], $permission_id);
            $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($arr);
        }else{
            $arr=['code'=>'1','status'=>'error','data'=>'角色名不能重复'];
            echo json_encode($arr);
        }
    }
    public function show(){
        $arr=DB::query("select id,name,description from role");
        $json=['code'=>'0','status'=>'ok','data'=>$arr];
        return json($json);
    }
    public function permissionShow(){
        $arr=DB::query("select p.id,p.name,p.description,p.path,p_c.name as pc_name,p.category_id from permission as p inner join permission_category as p_c on p.category_id=p_c.id");
        $newarr=[];
        foreach ($arr as $key => $value){
            $newarr[$value['pc_name']][]=$value;
        }
        $arr=['code'=>'0','status'=>'ok','data1'=>$newarr];
        return json($arr);
    }
    public function mypermissionShow(){
        $id=Request::get('id');
        $arr=DB::query("select permission_id from role_permission where role_id='$id'");
        $arr=['code'=>'0','status'=>'ok','data'=>$arr];
        return json($arr);
    }
    public function update(){
        $data=Request::post();
        $validate = new \app\admin\validate\Role;
        if (!$validate->check($data)) {
            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        $rbac= new Rbac;
        $name=$data['name'];
        $id=$data['id'];
        $permission_id=$data['permission_id'];
        $up_data=$data;
        unset($up_data['__token__']);
        unset($up_data['permission_id']);
        $arr=Db::query("select * from role where name='$name'");
        if (empty($getarr)||!empty($arr)&&$arr[0]['id']==$data['id']){
            $arr=Db::table('role')->update($up_data);
            $res=['code'=>'0','status'=>'ok','data'=>'修改成功'];
            $arr=Db::query("delete  from role_permission where role_id='$id'");

                $pid_arr=explode(',',$permission_id);
                array_shift($arr);
                foreach ($pid_arr as $key => $value){
                    $arr=Db::query("insert into `role_permission` (`role_id`,`permission_id`)values('$id','$value')");
                }
                $res=['code'=>'0','status'=>'ok','data'=>'修改成功'];
        }else{
            $res=['code'=>'1','status'=>'error','data'=>'角色名重复'];

        }
        return json($res);
    }
    public function delete(){
        $data=Request::post();
        $__token__=Request::post('__token__');
        if ($__token__!=Session::get('token')){
            $arr=['code'=>'1','status'=>'error','data'=>'令牌不匹配'];
            echo json_encode($arr);
            die;
        }
        $token=uniqid();
        Session::set('token',$token);
        $rbac= new Rbac;
        $rbac->delRole($data['id']);
        $res=['code'=>'0','status'=>'ok','data'=>'删除成功'];
        return json($res);
    }
}