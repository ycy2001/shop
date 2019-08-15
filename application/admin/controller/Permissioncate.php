<?php
namespace app\Admin\controller;
use gmars\rbac\Rbac;
use Request;
use think\Db;
use Session;

class Permissioncate extends Common
{
    public function list()
    {
        $token=uniqid();
        Session::set('token',$token);
        $this->assign('token',$token);
        return $this->fetch();
    }
    public function show(){
        $rbac= new Rbac;
        $arr=$rbac->getPermissionCategory([['status','=',1]]);
        echo json_encode($arr);
    }
    public function addAction(){
        $data=Request::post();
        $validate = new \app\admin\validate\Permissioncate;
        if (!$validate->check($data)) {
            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        $rbac= new Rbac;
        $arr=$rbac->getPermissionCategory([['name','=',$data['name']]]);
        if (empty($arr)){
           $rbac->savePermissionCategory([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => 1
            ]);
            $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($arr);
        }else{
            $arr=['code'=>'1','status'=>'error','data'=>'分类已存在'];
            echo json_encode($arr);
            die;
        }
    }
    public function delete(){
        $id=Request::post('id');
        $__token__=Request::post('__token__');
        if ($__token__!=Session::get('token')){
            $arr=['code'=>'1','status'=>'error','data'=>'令牌不匹配'];
            echo json_encode($arr);
            die;
        }
        $token=uniqid();
        Session::set('token',$token);
        $rbac= new Rbac;
        $rbac->delPermissionCategory([$id]);
        $arr=['code'=>'0','status'=>'ok','data'=>'删除成功','token'=>"$token"];
        echo json_encode($arr);
    }
    public function update(){
        $data=Request::post();
        $validate = new \app\admin\validate\Permissioncate;
        if (!$validate->check($data)) {
            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        $rbac= new Rbac;
        $getarr=$rbac->getPermissionCategory([['name','=',$data['name']]]);
        if (empty($getarr)){
            $sql=['name'=>$data['name'],'description'=>$data['description']];
            $arr=Db::table('permission_category')->where('id',$data['id'])->update($sql);
            $res=['code'=>'0','status'=>'ok','data'=>'修改成功'];
            echo json_encode($res);
        }else{

            if ($getarr[0]['id']!=$data['id']) {
                $arr=['code'=>'1','status'=>'error','data'=>'分类已存在'];
                echo json_encode($arr);
                die;
            }else{
                $sql=['name'=>$data['name'],'description'=>$data['description']];
                Db::table('permission_category')->where('id',$data['id'])->update($sql);
                $res=['code'=>'3','status'=>'ok','data'=>'修改成功'];
                echo json_encode($res);
                die;
            }
        }
    }
    public function deleteMore(){
        $id=Request::post('id');
        $__token__=Request::post('__token__');
        if ($__token__!=Session::get('token')){
            $arr=['code'=>'1','status'=>'error','data'=>'令牌不匹配'];
            echo json_encode($arr);
            die;
        }
        if (empty($id)){
            $arr=['code'=>'0','status'=>'error','data'=>'不能为空'];
            echo json_encode($arr);
            die;
        }
        $arr=explode(',',$id);
        array_shift($arr);
        $token=uniqid();
        Session::set('token',$token);
        $rbac= new Rbac;
        $rbac->delPermissionCategory($arr);
        $arr=['code'=>'0','status'=>'ok','data'=>'删除成功','token'=>"$token"];
        echo json_encode($arr);
    }
}