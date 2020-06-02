<?php


namespace app\book\model;
use think\Model;
use traits\model\SoftDelete;        //实现软删除
use app\author\model\Author;

class Article extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';
    protected $insert = ['article_id'];
    protected function setArticleIdAttr(){
        return '1'.substr(microtime(true),4,6);
    }

    protected function getThumbAttr($value){
        if ($value){
            return $value;
        }else{
            return "/static/book/images/default.png";
        }
    }
    protected function getCategoryIdAttr($value){
        switch ($value){
            case 10101:
                $cate = '流行小说';
                break;
            case 10102:
                $cate = '国外小说';
                break;
            case 10103:
                $cate = '社科人文';
                break;
            case 10104:
                $cate = '国学名著';
                break;
            case 10105:
                $cate = '儿童读物';
                break;
            case 10106:
                $cate = '杂文选集';
                break;
            case 10107:
                $cate = '纪实传记';
                break;
            case 10108:
                $cate = '经济管理';
                break;
            case 10109:
                $cate = '外语读本';
                break;
            case 10110:
                $cate = '成功励志';
                break;
            case 10111:
                $cate = '言情小说';
                break;
            case 10112:
                $cate = '科幻小说';
                break;
            case 10113:
                $cate = '惊悚悬疑';
                break;
            case 10114:
                $cate = '武侠小说';
                break;
            case 10115:
                $cate = '侦探推理';
                break;
            case 10116:
                $cate = '历史小说';
                break;
            default:
                $cate = '流行小说';
        }
        return $cate;
    }
    public function author(){
        return $this->belongsTo('app\author\model\Author','author_id','role_id')->field('role_id,nickname');
    }
    public function chapter(){
        return $this->hasMany('app\book\model\Chapter','article_id','article_id')->field('chapter_url,chapter_id,title')->order('chapter_id');
    }
    public function com(){
        return $this->hasMany('app\book\model\Comment','article_id','article_id');
    }
    public function bookshelf(){
        return $this->hasMany('app\member\model\Bookshelf','article_id','article_id');
    }
}