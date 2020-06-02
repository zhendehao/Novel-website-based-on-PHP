<?php


namespace app\admin\validate;
use think\Validate;

class Info extends Validate
{
    protected $rule = [
        'sender' => 'require',
        'recipient'=> 'require',
        'title'=> 'require',
        'content'=> 'require',

    ];
    protected $message = [
        'sender.require'=>'发送者不能为空',
        'recipient.require'=>'接收者不能为空',
        'title.require'=>'主题不能为空',
        'content.require'=>'内容不能为空',
    ];
}