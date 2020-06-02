<?php


namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;

class SysInfo extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';
//    public function Member(){
//        return $this->hasMany('app\member\model\Member','role_id','recipient');
//    }
}