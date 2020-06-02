<?php
namespace app\index\controller;

use app\book\model\Article;
use app\book\model\Fav;
use think\Controller;


class Index extends Controller
{
    public function index()
    {
        $title = '基于php的电子书推荐与阅读系统';
        $newList = Article::limit(10)->order('create_time','desc')->select();

        $hotIdList = Fav::limit(10)->field('article_id')->order('views','desc')->select()->toArray();
        $idList = [];
        foreach ($hotIdList as $key=>$id){
            $idList[$key] = $id['article_id'];
        }

        $hotList = Article::all($idList);

        $recommendList = Article::where('push',1)->limit(10)->select();
        $favIdList = Fav::limit(10)->field('article_id')->order('favorite','desc')->select()->toArray();
        $idList = [];
        foreach ($favIdList as $key=>$id){
            $idList[$key] = $id['article_id'];
        }
        $favList = Article::all($idList);
        $this->assign(['title'=>$title,'newList'=>$newList,'hotList'=>$hotList,'recommendList'=>$recommendList,'favList'=>$favList]);

        return $this->fetch();


    }



}
