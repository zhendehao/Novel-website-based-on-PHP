<?php


namespace app\book\model;
use think\Model;
use traits\model\SoftDelete;

class Comment extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';

    public function author(){
        return $this->belongsTo('app\author\model\Author','user_id','role_id');
    }
    public function reply(){
        return $this->hasMany('Comment','reply_id','comment_id')->selfRelation(true);
    }

    public function user(){
        return $this->belongsTo('app\member\model\Member','user_id','role_id')->field('role_id,nickname,thumb');
    }







}