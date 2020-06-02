<?php


namespace app\book\model;
use think\Model;

class Fav extends Model
{

    public function article(){
        return $this->hasMany('app\book\model\Article','article_id','article_id');
    }
}