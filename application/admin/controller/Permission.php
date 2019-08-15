<?php
namespace app\Admin\controller;
use gmars\rbac\Rbac;
use Request;
use think\Db;
use Session;
class Permission extends Common
{
    public function list()
    {
        $token=uniqid();
        Session::set('token',$token);
        $this->assign('token',$token);
        return $this->fetch();
    }
    public function show(){
        $arr=DB::query("select p.id,p.name,p.description,p.path,p_c.id as pc_id,p_c.name as pc_name from permission as p inner join permission_category as p_c on p.category_id=p_c.id");
        echo json_encode($arr);
    }
    public function showcate(){
        $rbac= new Rbac;
        $arr=$rbac->getPermissionCategory([['status','=',1]]);
        echo json_encode($arr);
    }
    public function addAction(){
        $data=Request::post();
        $validate = new \app\admin\validate\Permission;
        if (!$validate->check($data)) {
            $arr=['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        $rbac= new Rbac;
        $arr=$rbac->getPermission([['name','=',$data['name']]]);
        $arrp=$rbac->getPermission([['path','=',$data['path']]]);
        if (empty($arr)&&empty($arrp)){
            $rbac->createPermission([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => 1,
                'type' => 1,
                'category_id' => $data['categroy_id'],
                'path' => $data['path'],
            ]);
            $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($arr);
        }else if ($arrp!=""||$arrp!=""){
            $arr=['code'=>'1','status'=>'error','data'=>'权限或路径已存在'];
            echo json_encode($arr);
            die;
        }
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
        $arr=$rbac->getPermission([['name','=',$data['name']]]);
        $arrp=$rbac->getPermission([['path','=',$data['path']]]);
        if (empty($arr)&&empty($arrp)){
            $sql=['name'=>$data['name'],'description'=>$data['description'],'path'=>$data['path'],'category_id'=>$data['pc_id']];
            $arr=Db::table('permission')->where('id',$data['id'])->update($sql);
            $res=['code'=>'0','status'=>'ok','data'=>'修改成功'];
            echo json_encode($res);
            die;
        }else{
            if ($arr[0]['id']!=$data['id']) {
                $arr=['code'=>'1','status'=>'error','data'=>'权限已存在'];
                echo json_encode($arr);
                die;
            }else if ($arrp[0]['path']==$data['path']){
                $arr=['code'=>'1','status'=>'error','data'=>'路径已存在'];
                echo json_encode($arr);
                die;
            }else{
                $sql=['name'=>$data['name'],'description'=>$data['description'],'path'=>$data['path'],'category_id'=>$data['pc_id']];
                Db::table('permission')->where('id',$data['id'])->update($sql);
                $res=['code'=>'3','status'=>'ok','data'=>'修改成功'];
                echo json_encode($res);
                die;
            }
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
        $arr=Db::table('permission')->where('id',$id)->delete();
        $arr=['code'=>'0','status'=>'ok','data'=>'删除成功','token'=>"$token"];
        echo json_encode($arr);
    }
    public function deleteMore(){
        $id=Request::post('id');
        $mid=explode(",",$id);
        array_shift($mid);
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
        $rbac->delPermission($mid);
        $arr=['code'=>'0','status'=>'ok','data'=>'删除成功','token'=>"$token"];
        echo json_encode($arr);
    }
}