<?php


namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;        //实现软删除

class Admin extends Model
{
    use SoftDelete;
    //自动删除时需要修改的字段
    protected static $deleteTime = 'delete_time';
    //用于存放需要进行自动处理的属性（数据库字段名）
    //protected $auto = [''];
    //插入新数据时才自动处理
    protected $insert = ['role_id'];
    protected function setRoleIdAttr(){

        return '11'.substr(time(),5,5);
    }
    //修改器，setFieldNameAttr 没有Protected，对数据进行处理然后操作
    //自动完成是对数据库字段的填充，不应该有数据的输入，不然会进行两次处理操作

    //修改器，对数据进行后续处理的一种方式
    //
    //自动完成，自动对数据进行填充的一种方式
    protected function setPasswordAttr($value){
        return substr(md5($value),1,30);
    }
}