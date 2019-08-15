<?php
namespace app\Admin\controller;
use think\Controller;
use think\facade\Session;
use Request;
use gmars\rbac\Rbac;
use Redis;
class Common extends Controller
{
    public function __construct(){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $num=$redis->get('number');
        if (empty($num)){
            $redis->set("number", "1");
        }else{
            $num=$num+1;
            $redis->set('number', "$num");
        }
        parent::__construct();
        $a=Session::get('name');
        $this->assign('name',$a);
        if (empty($a)){
            $this->redirect('login/login');
        }
        //权限验证
        $module=Request::module();
        $class=Request::controller();
        $action=Request::action();
        $arr_class=['Permission','Permissioncate','Role','User'];
        $arr_action=['show','addaction','delete','update'];
        if (in_array($class,$arr_class)){
            if (in_array($action,$arr_action)){
                $str="$module/$class/$action";
                $str=strtolower($str);
                $rbac= new Rbac();
                $bool=$rbac->can($str);
                if ($bool==false){
                    header("Content-Type:application/json");
                    $arr=['code'=>'10001','status'=>'error','data'=>'没有权限','a'=>$module,'b'=>$class,'c'=>$action];
                    echo json_encode($arr);
                    die;
                }
            }
        }

    }
    public function token(){
        $token = $this->request->token('__token__', 'sha1');
        $arr=['token'=>$token];
        echo json_encode($arr);
    }
}