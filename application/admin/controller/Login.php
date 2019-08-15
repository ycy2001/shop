<?php
namespace app\Admin\controller;
use think\Db;
use think\facade\Session;
use gmars\rbac\Rbac;
use Request;
class Login extends \think\Controller
{
    public function login()
    {
        return $this->fetch('login');
    }
    public function logindo(){
        $rbac=new Rbac();
        $name=Request::post('name');
        $pwd=Request::post('pwd');
        $code=Request::post('yzm');
        $password=md5($pwd);
        $show=Db::table('user')->where('user_name',$name)->where("password",$password)->find();
        if (!captcha_check($code)) {
            $arr['status']='error';
            $arr['code']='0';
            $json=json_encode($arr);
            echo $json;
            die;
        }else if ($show=="") {
            $arr['status']='error';
            $arr['code']='1';
            $json=json_encode($arr);
            echo $json;
            die;
        }else if ($show!=""){
            $arr['status']='success';
            $arr['code']='2';
            session::set('name',"$name");
            $rbac->cachePermission($show['id']);
        }
        $json=json_encode($arr);
        echo $json;
        die;
    }
    public function loginexit(){
        Session::clear();
        $this->redirect('login/login');
    }
}