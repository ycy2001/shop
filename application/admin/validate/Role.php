<?php

namespace app\admin\validate;

use think\Validate;

class Role extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name'  => 'require|max:50|min:1|token',
        'description'=>'require|max:50|min:1',
        'permission_id'   => 'require|min:1',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '角色名不能为空',
        'name.max'     => '角色名最多不能超过50个字符',
        'name.min'     => '角色名最多不能少于1个字符',
        'description.require' => '角色描述不能为空',
        'description.max'     => '角色描述最多不能超过50个字符',
        'description.min'     => '角色描述最多不能少于1个字符',
        'permission_id.require' => '权限不能为空',
        'permission_id.min'     => '权限最多不能少于1个字符',
    ];
}
