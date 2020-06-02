<?php


namespace app\author\controller;
use app\author\controller\Base;
use app\book\model\Article as ArticleModel;
use app\book\model\Chapter;
use app\book\model\Comment;

class Del extends Base
{


    public function del(){
        $article_id = input('article_id/d');
        $force = input('force',0);

        ArticleModel::destroy($article_id,$force);

        if ($force){
            Chapter::where('article_id','=',$article_id)->delete();
            Comment::where('article_id','=',$article_id)->delete();
        }
        if(session('role')=='admin')
            $this->redirect('admin/user/manage_books');
        else
            $this->redirect('author/author/works');


    }
    public function recover(){
        $article_id = input('article_id/d');
        $article = ArticleModel::onlyTrashed()->find($article_id);
        $result = $article->restore();
        $this->redirect('author/author/works');
    }


}