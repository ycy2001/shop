<?php
namespace app\Admin\controller;
use function Composer\Autoload\includeFile;
use Request;
use think\Db;
use Redis;
class Productlist extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function productlistshow()
    {
        $id=Request::get('id');
        $attr_category_id=Request::get('attr_category_id');
        $this->assign('id',$id);
        $this->assign('attr_category_id',$attr_category_id);
        return $this->fetch();
    }
    public function goods_add()
    {
        $id=Request::get('id');
        $attr_category_id=Request::get('attr_category_id');
        $this->assign('id',$id);
        $this->assign('attr_category_id',$attr_category_id);
        return $this->fetch();
    }
    public function shop_goods_show()
    {
        $id=Request::get('id');
        $this->assign('id',$id);
        return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
    public function show(){
        $redis = new Redis();
        $data=Request::post();
        $shop_name=strtolower($data['shop_name']);
        $redis->connect('127.0.0.1', 6379);
        if (empty($shop_name)){
            $arr=Db::query("select goods.id as gid,goods_name,category.name as cname,brand.name as bname,attr_category_id FROM `goods` inner join brand on goods.brand_id=brand.id inner join category on goods.category_id=category.id where goods.online_status=1 order by goods.id asc limit 0,300");
        }else{
            $arr=Db::query("select goods.id as gid,goods_name,category.name as cname,brand.name as bname,attr_category_id FROM `goods` inner join brand on goods.brand_id=brand.id inner join category on goods.category_id=category.id where goods.online_status=1 and goods_name like '%$shop_name%' order by goods.id asc limit 0,300");
            $arr=json_encode($arr);
            $num=$redis -> hGet("goods","$arr");
            if ($num=10){
                $redis -> hset("goods_arr","$shop_name","$arr");
                $arr=$redis -> hGet("goods_arr","$shop_name");
                $arr=json_decode($arr);
                $num=$num+1;
                $redis->zAdd("top", "$num", "$shop_name");
                $a=$redis->zRevRange('top', 0, -1);
                $json=['code'=>'0','status'=>'ok','data'=>$arr,'top'=>$a,'file'=>'redis'];
                return json($json);
                die;
            }
            if(empty($num)){
                $redis -> hSet("goods","$arr","1");
                $redis->zAdd("top", 1, "$shop_name");
            }else{
                $num=$num+1;
                $redis -> hSet("goods","$arr","$num");
                $redis->zAdd("top", "$num", "$shop_name");
            }
            $arr=json_decode($arr);
        }
        $a=$redis->zRevRange('top', 0, -1,'withscore');
        if (empty($a)){
            $json=['code'=>'0','status'=>'ok','top'=>'没有数据','data'=>$arr];
            return json($json);
        }else{
            $json=['code'=>'0','status'=>'ok','top'=>$a,'data'=>$arr];
            return json($json);
        }

    }
    public function show_brand(){
        $arr=DB::query("select * from brand");
        echo json_encode($arr);
    }
    public function show_attrcate(){
        $arr=DB::query("select * from attr_category");
        echo json_encode($arr);
    }
    public function show_category(){
        $arr=DB::query("select * from category");
        $this->getTree($arr);
    }
    private function getTree($arr,$pid = 0, $level = 0){


        static $list = [];

        foreach ($arr as $key => $value){
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['pid'] == $pid){
                //父节点为根节点的节点,级别为0，也就是第一级
                $flg = str_repeat('|--',$level);
                // 更新 名称值
                $value['name'] = $flg.$value['name'];
                // 输出 名称
                echo "<option value=".$value['id'].">".$value['name']."</option>";
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($arr[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTree($arr, $value['id'], $level+1);
            }
        }
        return $list;
    }
    public function data(){
        $a=date("Ymd");
        $b=rand(10000,99999);
        $c=$a.$b;
    }
    public function addAction(){
        $data=Request::post();
        $atid=$data['attr_id'];
        $goods_name=$data['goods_name'];
        $category_id=$data['category_id'];
        $brand_id=$data['brand_id'];
        $a=date("Ymd");
        $b=rand(10000,99999);
        $goods_number=$a.$b;
//        echo $goods_number;
//        $validate = new \app\admin\validate\Permission;
//        if (!$validate->check($data)) {
//            $arr=['code'=>'1','status'=>'error','data'=>$validate->getError()];
//            echo json_encode($arr);
//            die;
//        }
        $add = Db::query("insert into `goods` (`goods_name`,`category_id`,`brand_id`,`goods_number`)values('$goods_name','$category_id','$brand_id','$goods_number')");
        $arr=Db::query("select * from goods where goods_number='$goods_number'");
        $goods_id=$arr[0]['id'];
//           var_dump($arr) ;die;
        $arr=explode(',',$atid);
        array_shift($arr);
        foreach ($arr as $key => $value) {
            $arr=Db::query("select * from attr_detaile where id='$value'");
            $aid=$arr[0]['id'];
            $cate_id=$arr[0]['attr_id'];
            $data=['goods_id'=>$goods_id,'attr_details_id'=>$aid,'attr_id'=>$cate_id];
            Db::table('goods_attr')->insert($data);
        }
        $res=['code'=>'0','status'=>'ok','data'=>'添加成功'];
        echo json_encode($res);

    }
    public function imgadd(){
        $id=Request::post('id');
        $data=date('Ymd');
        $file=request()->file('file');
        foreach ($file as $key=>$value){
            $info = $value->move('static/image/goods');
            if ($info){
                $getSaveName=str_replace("\\","/",$info->getSaveName());
                $image = \think\Image::open('static/image/goods/'.$getSaveName);
                $name=$info->getFilename();
                $big="in$name";
                $middle="im$name";
                $small="ig$name";
                $image->thumb(150,150)->save("static/image/goods/in$name");
                $image->thumb(100,100)->save("static/image/goods/im$name");
                $image->thumb(50,50)->save("static/image/goods/ig$name");
                $where=['img_big'=>$big,'img_middle'=>$middle,'img_small'=>$small,'goods_id'=>$id];
                Db::table('goods_img')->insert($where);
                $arr = ['code' => '0', 'status' => 'ok', 'data' => '添加成功'];
                echo json_encode($arr);
            }else{
                echo $file->getError();
            }
        }
    }
    public function imgshow(){
        $id=Request::post('id');
        $arr=Db::query("select id,img_big,goods_id FROM `goods_img` where goods_id=$id");
        $json=['code'=>'0','status'=>'ok','data'=>$arr];
        return json($json);
    }
    public function img_delete(){
        $id=Request::post('id');
        $arr=Db::query("select img_big,img_middle,img_small FROM `goods_img` where id=$id");
        $img_big=$arr[0]['img_big'];
        $img_middle=$arr[0]['img_middle'];
        $img_small=$arr[0]['img_small'];
        unlink("static/image/goods/$img_big");
        unlink("static/image/goods/$img_middle");
        unlink("static/image/goods/$img_small");
        $arr=Db::query("delete  from goods_img where id=$id");
        $json=['code'=>'0','status'=>'ok','data'=>$arr];
        return json($json);
    }
    public function attrShow(){
        $data=Request::post();
        $goods_id=$data['goods_id'];
        $arr=DB::query("select attribute.name as aname,attr_detaile.id as atid,attr_detaile.name as atname from goods_attr inner join attribute on goods_attr.attr_id=attribute.id inner join attr_detaile on goods_attr.attr_details_id=attr_detaile.id where goods_attr.goods_id='$goods_id'");
        $newarr=[];
        foreach ($arr as $key => $value){
            $newarr[$value['aname']][]=$value;
        }
        $arr=['code'=>'0','status'=>'ok','data'=>$newarr];
        return json($arr);
    }
    public function attr(){
        $data=Request::post();
        $attrcate_id=$data['attrcate_id'];
        $arr=DB::query("select attribute.id as aid,attribute.name as aname,attr_detaile.id as atid,attr_detaile.name as atname from attribute inner join attr_detaile on attribute.id =attr_detaile.attr_id where attribute.attrcate_id='$attrcate_id'");
        $newarr=[];
        foreach ($arr as $key => $value){
            $newarr[$value['aname']][]=$value;
        }
        $arr=['code'=>'0','status'=>'ok','data'=>$newarr];
        return json($arr);
    }
    public function shop_goods_add(){
        $data=Request::post();
        $goods_id=$data['goods_id'];
        $price=$data['price'];
        $number=$data['number'];
        $attr_id=$data['attr_id'];
        $arr=explode(',',$attr_id);
        array_shift($arr);
        $a=implode(',',$arr);
        $add = Db::query("insert into `shop_goods` (`goods_id`,`goods_attr_id`,`price`,`number`)values('$goods_id','$a','$price','$number')");
        $res=['code'=>'0','status'=>'ok','data'=>'添加成功'];
        echo json_encode($res);
    }
    public function show_shop_goods(){
        $data=Request::post();
        $id=$data['goods_id'];
        $show=Db::query("select * from shop_goods where goods_id=$id");
        for ($i=0;$i<count($show);$i++){
            $abb='';
            $arr=$show[$i]['goods_attr_id'];
            $new_attr=explode(',',$arr);
            for ($j=0; $j <count($new_attr); $j++){

                $new_de=Db::query("select * from attr_detaile where id='$new_attr[$j]'");
                $abb=$abb.' '.$new_de[0]['name'];
            }
            $show[$i]['key']=$abb;

        }
//var_dump($show);
        $res=['code'=>'0','status'=>'ok','data'=>$show];
        echo json_encode($res);
    }
    private function getChar()  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<4; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }
    public function shop_add_more(){
        for ($i=0;$i<30000;$i++){
            $goods_name=$this->getChar();
            $category_id=13;
            $brand_id=1;
            $a=date("Ymd");
            $b=rand(10000,99999);
            $goods_number=$a.$b;
            Db::query("insert into `goods` (`goods_name`,`category_id`,`brand_id`,`goods_number`,`attr_category_id`)values('$goods_name','$category_id','$brand_id','$goods_number','1')");
        }
    }
}