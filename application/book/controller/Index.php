<?php


namespace app\book\controller;
use app\book\model\Chapter;
use app\book\model\Comment;
use app\book\model\Fav;
use app\member\model\Bookshelf;
use think\Collection;
use think\Controller;
use app\book\controller\Base;
use app\book\model\Article;


class Index extends Base
{
    protected $is_check_login = ['read'];
    public function cate($id){
    $title = '图书分类';
    $list = Article::where('category_id',$id)->paginate();
    $page = $list->render();

    $this->assign(['title'=>$title,'list'=>$list,'page'=>$page]);
    return $this->fetch();
    }

    public function info($id){
        $title = '图书详情';
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $details = Article::get($id);
        $tag = $details->tags;
        $tags = explode('，',$tag);

        $fav = Fav::get($id);
        Fav::where('article_id',$id)->setInc('views');
        $comments = Comment::where('article_id',$id)->select();

        $likes = Article::where('author_id',$details->author_id)->field('article_id,author_id,title,thumb')->limit(5)->select();

        $this->assign(['title'=>$title,'info'=>$info,'details'=>$details,'tags'=>$tags,'fav'=>$fav,'comments'=>$comments,'likes'=>$likes]);
        return $this->fetch();
    }
    public function portfolio($id){
        $title = '作品集';
        $portfolio = Article::where('author_id',$id)->paginate();
        $page = $portfolio->render();
        $this->assign(['title'=>$title,'portfolio'=>$portfolio,'page'=>$page]);
        return $this->fetch();
    }
    public function read($id){
        $title = '电子书阅读';
        $content = Chapter::get($id);
        $bookshelf = Bookshelf::where('article_id',$content->article_id)->where('member_id',session('role_id'))->find();
        if ($bookshelf){
            $bookshelf->chapter_url = $id;
            $bookshelf->save();
        }
        $this->assign(['title'=>$title,'content'=>$content]);
        return $this->fetch();
    }
}