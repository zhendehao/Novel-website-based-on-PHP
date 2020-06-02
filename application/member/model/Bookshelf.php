<?php


namespace app\member\model;
use think\Model;
use traits\model\SoftDelete;

class Bookshelf extends Model
{

    use SoftDelete;
    protected static $deleteTime = 'delete_time';
    public function article(){
        return $this->belongsTo('app\book\model\Article','article_id','article_id');
    }
    public function chapter(){
        return $this->belongsTo('app\book\model\Chapter','chapter_url','chapter_url');
    }
}