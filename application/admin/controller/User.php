<?php
namespace app\Admin\controller;
use think\facade\Session;
use Request;
use think\Db;
use gmars\rbac\Rbac;
class User extends Common
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
    public function showcate(){
        $rbac= new Rbac;
        $arr=$rbac->getRole([], true);
        echo json_encode($arr);
    }
    public function addAction(){
        $data=Request::post();
//        $validate = new \app\admin\validate\Role;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $rbac= new Rbac;
        $name=$data['name'];
        $password=md5($data['password']);
        $mobile=$data['mobile'];
        $role_id=$data['role_id'];
        $time=date('Y-m-d H:i:s', time());
        $getname=Db::query("select * from user where user_name='$name'");
        if (empty($getname)){
            Db::query("insert into `user` (`user_name`,`password`,`mobile`,`create_time`,`update_time`)values('$name','$password','$mobile','$time','$time')");
            $uid=Db::query("select * from user where user_name='$name'");
            $id=$uid[0]['id'];
            $rbac->assignUserRole($id, [$role_id]);
            $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($arr);
        }else{
            $arr=['code'=>'1','status'=>'error','data'=>'管理员不能重复'];
            echo json_encode($arr);
        }
    }
    public function show(){
        $arr=DB::query("select user.id,user.password, user.user_name,user.mobile,user.create_time,user.last_login_time,user.update_time,role.name,role.id as role_id  from user left join user_role on user.id=user_role.user_id join role on user_role.role_id=role.id");
        $json=['code'=>'0','status'=>'ok','data'=>$arr];
        return json($json);
    }
    public function update(){
        $data=Request::post();
//        $validate = new \app\admin\validate\Role;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $rbac= new Rbac;
        $id=$data['id'];
        $user_name=$data['user_name'];
        $mobile=$data['mobile'];
        $role_id=$data['role_id'];
        $update_time=date('Y-m-d H:i:s', time());
        $arr=Db::query("select * from user where user_name='$user_name'");
        if (empty($getarr)||!empty($arr)&&$arr[0]['id']==$data['id']){
            $sql=['user_name'=>$user_name,'mobile'=>$mobile,'update_time'=>$update_time];
            $arr=Db::table('user')->where('id',$id)->update($sql);
            $ar=['role_id'=>$role_id];
            $arr=Db::table('user_role')->where('user_id',$id)->update($ar);
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