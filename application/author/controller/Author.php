<?php


namespace app\author\controller;
use app\admin\model\SysInfo;
use app\admin\model\UserInfo;
use app\book\model\Article;
use app\book\model\Chapter;
use app\book\model\Fav;
use think\Controller;
use app\author\controller\Base;
use app\author\model\Author as AuthorModel;
use app\member\model\Member as MemberModel;
use app\book\model\Article as ArticleModel;

use app\admin\validate\Register as RegValidate;
use change\Change;
use think\Db;
use think\Request;

class Author extends Base
{
    protected $is_check_login=['*'];
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $info['recipient'] = session('role_id');
        $info['read'] = 0;
        $sysInfoNum = SysInfo::where($info)->count();
        $userInfoNum = UserInfo::where($info)->count();
        $infoNum = $sysInfoNum+$userInfoNum;
        $this->assign(['sysInfoNum'=>$sysInfoNum,'userInfoNum'=>$userInfoNum,'infoNum'=>$infoNum]);
    }

    public function index(){
        $title = "我的主页";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $this->assign(['title'=>$title,'info'=>$info]);
        return $this->fetch();
    }

    public function changeInfo(){
        $role = input('role',null,'htmlspecialchars');
        $role_id = input('role_id');
        $return = input('return','','urldecode');
        $nickname = input('nickname',null,'htmlspecialchars');
        $sign = input('sign',null,'htmlspecialchars');

        $alter = new Change();

        if ($sign != ''){
            $result = $alter->alterSign($role,$role_id,$sign);
            if ($result){
                session('sign',$sign);
                $info = '个性签名修改成功';
            }else{
                $info = '未知错误，请重试';
            }

        }

        if ($nickname != ''){
            $result = $alter->alterNickname($role,$role_id,$nickname);
            if ($result){
                session(null);
                $this->success('修改成功，请重新登录！','author/index/login');
            }else{
                $info = '未知错误，请重试';
            }

        }
        if (!isset($info)){
            $info = '';
        }

        $this->redirect($return, ['info' => $info]);
        //$this->error($info);

    }

    public function works(){
        $title = "我的作品";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $role_id = session('role_id');

        $works = ArticleModel::where('author_id',$role_id)->paginate(['var_page'=>'page1']);
        $page1 = $works->render();

        $softList = ArticleModel::onlyTrashed()->where('author_id',$role_id)->paginate(['var_page'=>'page2']);
        $page2 = $softList->render();
        $this->assign(['title'=>$title,'info'=>$info,'works'=>$works,'page1'=>$page1,'softList'=>$softList,'page2'=>$page2]);
        return $this->fetch();
    }

    public function add_article(){
        $title = "添加新文章";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $this->assign(['title'=>$title,'info'=>$info]);

        return $this->fetch();
    }
    public function checkArt(){
        $author_id = session('role_id');
        $cate = input('cate');
        $title = input('title','','htmlspecialchars');
        $image = request()->file('image');
        $summary = input('summary','','htmlspecialchars');
        $tags = input('tags','','htmlspecialchars');
        $tag = explode(',',$tags);

        if ($title == '') {
            $info = '作品名称不能为空';
            $this->redirect('author/add_article',['info'=>$info]);
        }
        if ($summary == ''){
            $info = '作品简介不能为空';
            $this->redirect('author/add_article',['info'=>$info]);
        }
        if (count($tag)>10){
            $info = '最多只能填写10个标签';
            $this->redirect('author/add_article',['info'=>$info]);
        }
        $path = __STATIC__.'\\'.'author'.'\\'.'thumb';
        $savename = $author_id. DS . md5(microtime(true));

        if ($image){
            $info = $image->validate(['size'=>1048576,'ext'=>'jpeg,jpg,png,gif'])->move($path,$savename);
            if ($info){
                $ext = $info->getExtension();
                $absolute = $path.'\\'.$savename.'.'.$ext;
                $prefix =substr($absolute,0,37);
                $thumb = substr($absolute,37);
            }else{
                $info = '上传的图片不符合要求，上传失败';
                $this->redirect('author/add_article',['info'=>$info]);
            }

        }else{
            $thumb = null;
        }


        $result = ArticleModel::create(['author_id'=>$author_id,'category_id'=>$cate,'title'=>$title,'summary'=>$summary,'thumb'=>$thumb,'tags'=>$tags]);
        if ($result){
            Fav::create(['article_id'=>$result->article_id]);
            $this->redirect('author/works');
        }else{
            $info = '未知错误，添加失败，请重试';
            $this->redirect('author/add_article',['info'=>$info]);

        }

    }
    public function add_chapter(){
        $title = "添加章节";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $article_id = input('article_id/d');
        $prefix = substr($article_id,1)*1000;
        $details = ArticleModel::withTrashed()->where('article_id',$article_id)->find();
        $last_chapter_id = $details->last_chapter_id;
        //取得最新的四个章节
        $chapter_url = array();
        for($i=0;$i<4&&$last_chapter_id>0;$i++){
            array_push($chapter_url, $prefix+$last_chapter_id);
            $last_chapter_id--;
        }
        if (count($chapter_url)>0){
            $chapterList = Chapter::field('chapter_url,chapter_id,title')->order('chapter_id','desc')->select($chapter_url);
        }else{
            $chapterList =0;
        }

        $fav = Fav::get($article_id);

        $this->assign(['title'=>$title,'details'=>$details,'chapterList'=>$chapterList,'fav'=>$fav,'info'=>$info]);
        return $this->fetch();
    }

    public function checkChapter(){
        $data['article_id'] = input('article_id/d');
        $data['chapter_id'] = input('chapter_id/d');
        $data['chapter_url'] = substr($data['article_id'],1)*1000+$data['chapter_id'];
        $data['title'] = input('title','','htmlspecialchars');
        $text = input('text','','htmlspecialchars');
        $file = Request()->file('file');
        if ($file){
            $result = $this->validate(['file'=>$file],['file'=>'fileExt:txt|fileSize:1048576'],['file.fileExt'=>'格式不对'],['file.fileSize'=>'文件最大上传1M']);
            if (true!==$result){
                $info = $result['file'];
                $this->redirect('author/author/add_chapter',['article_id'=>$data['article_id'],'info'=>$info]);
            }
            $result = $file->move('../upload');
            if ($result!==false) {
                $fileContent = $this->strToUtf8(file_get_contents($result->getRealPath()));
            }else{
                $info = '权限不足，文件不能移动';
                $this->redirect('author/author/add_chapter',['article_id'=>$data['article_id'],'info'=>$info]);
            }
        }

        if (isset($fileContent))
            $text = $fileContent."\r\n".$text;


        if (strlen($text)){
            $text_match = preg_split('/\r\n/',$text);
            foreach ($text_match as $key=>$line){
                $text_match[$key] = '<p>'.trim($line).'</p>';
            }
            $data['content'] = implode('',$text_match);
        }else{
            $info = '请输入章节内容';
            $this->redirect('author/author/add_chapter',['article_id'=>$data['article_id'],'info'=>$info]);
        }


        $chapter = new Chapter();
        $chapter->startTrans();
        $result = $chapter->allowField(true)->save($data);

        if ($result ===false){
            $chapter->rollback();
            $info = '未知错误，新增章节内容';
            $this->redirect('author/add_chapter',['info'=>$info]);
        }
        $article = new ArticleModel();
        $article->startTrans();
        $refresh = $article->save(['last_chapter_id'=>$data['chapter_id']],['article_id'=>$data['article_id']]);
        if ($refresh === false){
            $chapter->rollback();
            $article->rollback();
            $info = '未知错误，数据更新失败';
            $this->redirect('author/add_chapter',['info'=>$info]);
        }
        $chapter->commit();
        $article->commit();
        $info = '成功添加章节';
        $this->redirect('author/works',['info'=>$info]);
    }

    public function modify(){
        $title = "修改文章详情";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $article_id = input('article_id');
        $details = ArticleModel::where('article_id',$article_id)->find();
        $this->assign(['title'=>$title,'info'=>$info,'details'=>$details]);
        return $this->fetch();
    }
    public function checkModify(){
        $author_id = session('role_id');
        $article_id = input('article_id');
        $image = request()->file('image');
        $summary = input('summary','','htmlspecialchars');
        $tags = input('tags','','htmlspecialchars');
        $tag = explode(',',$tags);
        $data = [];
        if ($summary != ''){
            $data['summary'] = $summary;
        }
        if (count($tag)>10){
            $info = '最多只能填写10个标签';
            $this->redirect('author/author/modify',['article_id'=>$article_id,'info'=>$info]);
        }elseif($tags!=''){
            $data['tags'] = $tags;
        }
        $path = __STATIC__.'\\'.'author'.'\\'.'thumb';
        $savename = $author_id. DS . md5(microtime(true));

        if ($image){
            $info = $image->validate(['size'=>1048576,'ext'=>'jpeg,jpg,png,gif'])->move($path,$savename);
            if ($info){
                $ext = $info->getExtension();
                $absolute = $path.'\\'.$savename.'.'.$ext;
                $prefix =substr($absolute,0,37);
                $data['thumb'] = substr($absolute,37);

            }else{
                $info = '上传的图片不符合要求，上传失败';
                $this->redirect('author/author/modify',['article_id'=>$article_id,'info'=>$info]);
            }

        }
        if (!empty($data)){
            $book = new ArticleModel();
            $book = $book->where('article_id',$article_id)->where('author_id',$author_id)->find();
            $old = $book->getData('thumb');
            $result = $book->save($data);
            if ($result){
                if (isset($data['thumb']) && null!=$old){
                    unlink($prefix.$old);
                }
            }
            $this->redirect('author/author/works');
        }
        $info = '请输入内容再提交';
        $this->redirect('author/author/modify',['article_id'=>$article_id,'info'=>$info]);

    }
    public function change(){
        $title = "修改密码";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $this->assign(['title'=>$title,'info'=>$info]);
        return $this->fetch();
    }
    public function changePass(){
        $role = input('role',null,'htmlspecialchars');
        $role_id = input('role_id');
        $return = input('return','','urldecode');
        $password['oldPass'] = input('oldPass');
        $password['newPass'] = input('newPass');
        $password['check'] = input('check');
        if (strlen($password['newPass'])<6){
            $info = '新密码不能少于6位';
            $this->redirect($return,['info'=>$info]);
        }
        if ($password['newPass'] !=$password['check']){
            $info = '两次密码不一致';
            $this->redirect($return,['info'=>$info]);
        }
        $alter = new Change();
        $result = $alter->alterPassword($role,$role_id,$password['oldPass'],$password['newPass']);
        if ($result){
            $this->success('修改成功，请重新登录！',$role.'/'.'index/login');
        }else{
            $info = '原密码不正确 或者 原密码与新密码不能相同';
            $this->redirect($return,['info'=>$info]);
        }

    }
    public function thumb(){
        $title = "修改头像";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $this->assign(['title'=>$title,'info'=>$info]);
        return $this->fetch();
    }
    public function changeThumb(){

        $role = input('role',null,'htmlspecialchars');
        $role_id = input('role_id');
        $file = request()->file('image');
        if (null==$file){
            $this->redirect('author/author/thumb');
        }

        $path = __STATIC__.'\\'.$role.'\\'.'thumb';
        $savename = $role_id. DS . md5(microtime(true));

        $info = $file->validate(['size'=>1048576,'ext'=>'jpeg,jpg,png,gif'])->move($path,$savename);

        if ($info){
            $ext = $info->getExtension();
            $absolute = $path.'\\'.$savename.'.'.$ext;
            $prefix =substr($absolute,0,37);
            $thumb = substr($absolute,37);
            $user = AuthorModel::get($role_id);
            $old = $user->getData('thumb');
            $user->thumb = $thumb;
            $result = $user->save();
            if ($result){
                if (null!=$old){
                    unlink($prefix.$old);
                }

                session('thumb',$thumb);
                $this->redirect('author/index');
            }
        }else{
            $info = '上传的图片不符合规范，上传失败';
            $this->redirect('author/author/thumb',['info'=>$info]);
        }


    }

    public function upload(){
        return $this->fetch();
    }
    public function notice(){
        $title = "系统通知";
        $info['recipient'] = session('role_id');
        //$info['read'] = 0;
        $noticeList = SysInfo::where($info)->order('info_id','desc')->paginate(5);
        foreach ($noticeList as $key=>$value){
            if ($value->read == 0){
                $value->read = 1;
                $value->save();
            }
        }

        $this->assign(['title'=>$title,'noticeList'=>$noticeList]);
        return $this->fetch();
    }
    public function user_info(){
        $title = "@我";
        $date['recipient'] = session('role_id');
        //$info['read'] = 0;
        $infoList = UserInfo::where($date)->order('info_id','desc')->paginate(5);

        foreach ($infoList as $key=>$value){
            if ($value->read == 0){
                $value->read = 1;
                $value->save();
            }
        }

        $this->assign(['title'=>$title,'infoList'=>$infoList]);
        return $this->fetch();
    }
    public function insert()
    {
        $data = input('');
        $user = new AuthorModel();
        $ret = $user->allowField(true)->save($data);
        if ($ret) {
            $this->redirect('Author/login');
        } else {
            $this->error('未知错误，添加失败');
        }
    }



    function strToUtf8($str){
        $encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
        if($encode == 'UTF-8'){
            return $str;
        }else{
            return mb_convert_encoding($str, 'UTF-8', $encode);
        }
    }






}