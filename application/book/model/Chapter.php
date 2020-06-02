<?php


namespace app\book\model;
use think\Model;
use traits\model\SoftDelete;        //实现软删除

class Chapter extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';
//    protected function setChapterIdAttr($value){
//
//    }

    public function article(){
        return $this->belongsTo('app\book\model\Article','article_id','article_id');
    }
    public function bookshelf(){
        return $this->hasMany('app\member\model\Bookshelf','chapter_url','chapter_url');
    }
}