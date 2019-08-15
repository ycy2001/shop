<?php
namespace app\Admin\controller;
use think\facade\Session;
use Request;
class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }
}