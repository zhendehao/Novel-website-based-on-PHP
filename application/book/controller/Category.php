<?php


namespace app\book\controller;
use app\book\model\Article;
use think\Controller;

class Category extends Controller
{
    public function cate($cate){

        $list = Article::where('category_id',$cate)->paginate();
        $page = $list->render();
        $this->assign(['list'=>$list,'list'=>$list,'page'=>$page]);
        $this->fetch('novel/index');
    }
}