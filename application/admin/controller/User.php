<?php


namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Admin as AdminModel;
use app\admin\model\SysInfo as SysInfoModel;
use app\book\model\Article as ArticleModel;
use think\Db;
use think\exception\DbException;
use app\author\model\Author as AuthorModel;
use app\member\model\Member as MemberModel;
use change\Change;
use app\admin\validate\Info as InfoValidate;

class User extends Base
{
    protected $is_check_login = ['*'];
    function index(){

        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $title = "后台管理系统";
        $this->assign(['title'=>$title,'info'=>$info]);
        return $this->fetch();
    }
    public function add_admin(){
        $title = "添加管理员";

        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $adminList = AdminModel::where('level',1)->paginate(5);
        $page = $adminList->render();
        $this->assign(['title'=>$title,'info'=>$info,'adminList'=>$adminList,'page'=>$page]);
        return $this->fetch();
    }
    public function manage_member(){

        $title = "会员管理";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $memberList = MemberModel::where('delete_time',null)->paginate(5,false,['var_page'=>'page1']);
        $page1 = $memberList->render();

        $softDelList = MemberModel::onlyTrashed()->paginate(5,false,['var_page'=>'page2']);
        $page2 = $softDelList->render();

        $this->assign(['title'=>$title,'info'=>$info,'memberList'=>$memberList,'page1'=>$page1,'softDelList'=>$softDelList,'page2'=>$page2]);
        return $this->fetch();
    }
    public function manage_author(){
        $title = "作者管理";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $authorList = AuthorModel::where('delete_time',null)->paginate(5,false,['var_page'=>'page1']);
        $page1 = $authorList->render();

        $softDelList = AuthorModel::onlyTrashed()->paginate(5,false,['var_page'=>'page2']);
        $page2 = $softDelList->render();

        $this->assign(['title'=>$title,'info'=>$info,'authorList'=>$authorList,'page1'=>$page1,'softDelList'=>$softDelList,'page2'=>$page2]);

        return $this->fetch();
    }
    public function manage_books(){
        $title = "电子书管理";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }

        $bookList = ArticleModel::withTrashed()->where()->field('author_id,article_id,title,category_id,thumb')->paginate();
        $page = $bookList->render();
        $this->assign(['title'=>$title,'info'=>$info,'bookList'=>$bookList,'page'=>$page]);

        return $this->fetch();
    }
    public function push(){
        $article_id = input('article_id/d');
        $article = new ArticleModel();
        $result = $article->save(['push'=>1],['article_id'=>$article_id]);
        dump($result);
        exit;
        $info = '推送已标记';

        $this->redirect('admin/user/manage_books',['info'=>$info]);
    }
    public function notice(){
        $title = "发送通知";
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $role_id = input('role_id/d');
        $nickname = input('nickname');
        $this->assign(['title'=>$title,'role_id'=>$role_id,'nickname'=>$nickname,'info'=>$info]);
        return $this->fetch();
    }
    public function checkInfo(){
        $nickname = input('nickname');
        $data['sender'] = session('role_id');
        $data['recipient'] = input('recipient/d');
        $data['title'] = input('title','','htmlspecialchars');
        $data['content'] = input('content','','htmlspecialchars');
        $infoVal = new InfoValidate();
        if (!$infoVal->check($data)){
            $info = $infoVal->getError();
            $this->redirect('admin/user/notice',['info'=>$info,'role_id'=>$data['recipient'],'nickname'=>$nickname]);
        }
        $notice = new SysInfoModel();
        $result = $notice->allowField(true)->save($data);
        if ($result){
            $info = '成功发送';
            $this->redirect('admin/user/notice',['info'=>$info,'role_id'=>$data['recipient'],'nickname'=>$nickname]);
        }else{
            $info = '未知错误，发送失败';
            $this->redirect('admin/user/notice',['info'=>$info,'role_id'=>$data['recipient'],'nickname'=>$nickname]);
        }
    }
    public function manage_notice(){
        $title = '管理通知';
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $role_id = session('role_id');
        $noticeList = SysInfoModel::where('sender',$role_id)->paginate(5);

        $page = $noticeList->render();
        $this->assign(['title'=>$title,'info'=>$info,'noticeList'=>$noticeList,'page'=>$page]);
        return $this->fetch();
    }
    public function manage_comment(){
        $title = "评论管理";

        $this->assign(['title'=>$title]);
        return $this->fetch();
    }

    public function initPass(){
        $role = input('role',null,'htmlspecialchars');
        $role_id = input('role_id');
        $return = input('return','','urldecode');
        $alter = new Change();
        $result = $alter->initPass($role,$role_id);

        if ($result){
            $info = '密码初始化成功';
            $this->redirect($return,['info'=>$info]);
        }else{
            $info = '不可重复初始化';
            $this->redirect($return,['info'=>$info]);
        }

    }

    public function modify(){
        $title = '修改密码';
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $this->assign(['title'=>$title,'info'=>$info]);
        return $this->fetch();
    }
    public function changeNickname(){
        $role = input('role',null,'htmlspecialchars');
        $role_id = input('role_id');
        $return = input('return','','urldecode');
        $nickname = input('nickname',null,'htmlspecialchars');
        if ($nickname == ''){
            $info = '昵称不能为空';
            $this->redirect('user/index', ['info' => $info]);
        }

        $alter = new Change();
        $result = $alter->alterNickname($role,$role_id,$nickname);
        if ($result){
            $this->success('修改成功，请重新登录！',$return);
        }else {
            $info = '未知错误，请重试';
//            $this->redirect('user/index', ['info' => $info]);
            $this->error($info);
        }
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

}