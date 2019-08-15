<?php
namespace app\Admin\controller;
use Request;
use think\Db;
class Brand extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function show(){
        $arr=Db::query("select * from brand");
        $json=['code'=>'0','status'=>'ok','data'=>$arr];
        return json($json);
    }
    public function addAction(){
//                $data=Request::post();
//        $validate = new \app\admin\validate\Role;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $name=$_POST['name'];
        $textarea=$_POST['textarea'];
        if (isset($_FILES['myfile']) && !empty($_FILES['myfile'])) {
            //限制文件最大上传大小为4兆（1024*1024*4=4194304）
            if($_FILES['myfile']['size'] < 4194304 ||$_FILES['myfile']['size'] =0) {
                //获取文件后缀
                $string = strrev($_FILES['myfile']['name']);
                $array = explode('.', $string);
                $ex = strrev($array[0]);
                //判断上传文件格式
                $arr_img = ['jpg','jpeg','gif','png'];
                if (in_array($ex, $arr_img)) {
                    //定义文件路径
                    $upload_path = "static/image/brand/";
                    //拼接日期随机数重命名
                    $rename = date("Y") . date("m") . date("d") . date("H") . date("i") . date("s") . rand(100, 999) . "." . $ex;
                    //判断品牌是否存在
                    $arr = Db::query("select * from brand where name='$name'");
                    if (empty($arr)) {
                        //向数据库添加
                        $add = Db::query("insert into `brand` (`name`,`logo`,`textarea`)values('$name','$rename','$textarea')");
                        //移动文件到指定目录
                        if (move_uploaded_file($_FILES['myfile']['tmp_name'], $upload_path . $rename)) {
                            $arr = ['code' => '0', 'status' => 'ok', 'data' => '添加成功'];
                            echo json_encode($arr);
                        } else {
                            $arr = ['code' => '1', 'status' => 'error', 'data' => '添加失败'];
                            echo json_encode($arr);
                        }
                    } else {
                        $arr = ['code' => '1', 'status' => 'error', 'data' => '品牌已存在'];
                        echo json_encode($arr);
                    }

                }else{
                    $arr = ['code' => '1', 'status' => 'error', 'data' => '不是图片'];
                    echo json_encode($arr);
                }
            }else{
                $arr = ['code' => '1', 'status' => 'error', 'data' => '超过上传限制（4mb）'];
                echo json_encode($arr);
            }
        }

    }
    public function img_update(){
//                $data=Request::post();
//        $validate = new \app\admin\validate\Role;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $id=$_POST['id'];
        $logo=$_POST['logo'];
        if (isset($_FILES['myfile']) && !empty($_FILES['myfile'])) {
            //限制文件最大上传大小为4兆（1024*1024*4=4194304）
            if($_FILES['myfile']['size'] < 4194304 ||$_FILES['myfile']['size'] =0) {
                //获取文件后缀
                $string = strrev($_FILES['myfile']['name']);
                $array = explode('.', $string);
                $ex = strrev($array[0]);
                //判断上传文件格式
                $arr_img = ['jpg','jpeg','gif','png'];
                if (in_array($ex, $arr_img)) {
                    //定义文件路径
                    $upload_path = "static/image/brand";
                    //拼接日期随机数重命名
                    $rename = date("Y") . date("m") . date("d") . date("H") . date("i") . date("s") . rand(100, 999) . "." . $ex;
                    //移动文件到指定目录
                    if (move_uploaded_file($_FILES['myfile']['tmp_name'], $upload_path . $rename)) {
                        if (unlink("static/image/brand/$logo")){
                            $sql=['logo'=>$rename];
                            $arr=Db::table('brand')->where('id',$id)->update($sql);
                            $arr = ['code' => '0', 'status' => 'ok', 'data' => '修改成功'];
                            echo json_encode($arr);
                        }else{
                            $arr = ['code' => '1', 'status' => 'error', 'data' => '修改失败'];
                            echo json_encode($arr);
                        }

                    } else {
                        $arr = ['code' => '1', 'status' => 'error', 'data' => '修改失败'];
                        echo json_encode($arr);
                    }

                }else{
                    $arr = ['code' => '1', 'status' => 'error', 'data' => '不是图片'];
                    echo json_encode($arr);
                }
            }else{
                $arr = ['code' => '1', 'status' => 'error', 'data' => '超过上传限制（4mb）'];
                echo json_encode($arr);
            }
        }

    }
    public function update(){
        $data=Request::post();
//        $validate = new \app\admin\validate\Role;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'ok','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $name=$data['name'];
        $id=$data['id'];
        $textarea=$data['textarea'];
        $arr=Db::query("select * from brand where name='$name'");
        if (empty($arr)){
            $sql=['name'=>$name,'textarea'=>$textarea];
            $arr=Db::table('brand')->where('id',$id)->update($sql);
            $res=['code'=>'0','status'=>'ok','data'=>'修改成功'];
        }else{
            $sql=['textarea'=>$textarea];
            $arr=Db::table('brand')->where('id',$id)->update($sql);
            $res=['code'=>'0','status'=>'ok','data'=>'修改成功'];
        }
        return json($res);
    }
    public function delete(){
        $id=$_POST['id'];
//        $__token__=Request::post('__token__');
//        if ($__token__!=Se ssion::get('token')){
//            $arr=['code'=>'1','status'=>'error','data'=>'令牌不匹配'];
//            echo json_encode($arr);
//            die;
//        }
//        $token=uniqid();
//        Session::set('token',$token);
//        $rbac= new Rbac;
//        $rbac->delRole($data['id']);
        $arr=Db::query("delete  from brand where id=$id");
        $res=['code'=>'0','status'=>'ok','data'=>'删除成功'];
        return json($res);
    }
}