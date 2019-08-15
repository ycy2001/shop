<?php

namespace app\admin\validate;

use think\Validate;

class Permission extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name'  => 'require|max:50|min:1|token',
        'description'   => 'require|max:200|min:1',
        'path'=>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '权限名不能为空',
        'name.max'     => '权限名最多不能超过50个字符',
        'name.min'     => '权限名最多不能少于1个字符',
        'description.require' => '权限描述不能为空',
        'description.max'     => '权限描述最多不能超过200个字符',
        'description.min'     => '权限描述最多不能少于1个字符',
        'path.require'=>'路径不能为空',
    ];
}
