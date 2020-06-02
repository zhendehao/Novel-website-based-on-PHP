<?php


namespace app\author\model;
use think\Model;
use traits\model\SoftDelete;
use app\book\model\Article;

class Author extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';
    //protected $auto = ['password'];
    protected $insert = ['role_id'];
    protected function setRoleIdAttr(){
        return '33'.substr(time(),2,6);
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
    protected function getRoleIdAttr($value){
        return $value;
    }

    protected function getSignAttr($value){
        if ($value){
            return $value;
        }else{
            return "设置您的个性签名";
        }
    }
    //关联操作
    public function article(){
        return $this->hasMany('app\book\model\Article','author_id','role_id');
    }
    public function com(){
        return $this->hasMany('app\book\model\Comment','user_id','role_id');
    }
}