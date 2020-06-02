<?php


namespace app\member\model;
use think\Model;
use traits\model\SoftDelete;        //实现软删除


class Member extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';

    //用于存放需要进行自动处理的属性（数据库字段名）
    //protected $auto = ['password'];
    protected $insert = ['role_id'];
    protected function setRoleIdAttr(){
        return '22'.substr(time(),4);
    }
    protected function setPasswordAttr($value){
        return substr(md5($value),1,30);
    }
    protected function getThumbAttr($value){
        if ($value){
            return $value;
        }else{
            return "/static/book/images/default.png";
        }
    }
    protected function getSignAttr($value){
        if ($value){
            return $value;
        }else{
            return '设置您的个性签名';
        }
    }
    public function comments(){
        return $this->hasMany('app\book\model\Comment','user_id','role_id')->field('role_id,nickname,thumb');
    }
}