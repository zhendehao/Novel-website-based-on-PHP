<?php


namespace app\admin\validate;
use think\Validate;

class Register extends Validate
{
    protected $rule = [
        'nickname|用户名' => 'require',
        'password|密码' => 'require|min:6|confirm:password2',
        'email|邮箱' => 'require',
    ];

    protected $message = [
        'nickname.require'=>'用户名不能为空',
        'password.require'=>'密码不能为空',
        'password.min'=>'密码长度不能少于6位',
        'password.confirm'=>'两次密码不一致',
        'email.require'=>'邮箱不能为空',
    ];
}