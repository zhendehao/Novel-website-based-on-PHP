<?php


namespace app\member\controller;
use app\admin\model\SysInfo;
use app\admin\model\UserInfo;
use app\book\model\Comment;
use app\book\model\Fav;
use app\member\model\Bookshelf;
use think\Controller;
use app\member\controller\Base;
use app\member\model\Member as MemberModel;
use app\book\model\Article as ArticleModel;
use change\Change;

class Member extends Base
{

    protected $is_check_login = ['*'];
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $info['recipient'] = session('role_id');
        $info['read'] = 0;
        $sysInfoNum = SysInfo::where($info)->count();
        $userInfoNum = UserInfo::where($info)->count();
        $infoNum =$sysInfoNum+$userInfoNum;
        $this->assign(['sysInfoNum'=>$sysInfoNum,'userInfoNum'=>$userInfoNum,'infoNum'=>$infoNum]);
    }

    public function index(){
        $title = "用户主页";
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
                $this->success('修改成功，请重新登录！','member/index/login');
            }else{
                $info = '未知错误，请重试';
            }

        }
        if (!isset($info)){
            $info = '';
        }
        $this->redirect($return, ['info' => $info]);

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

        $path = __STATIC__.'\\'.$role.'\\'.'thumb';
        $savename = $role_id. DS . md5(microtime(true));

        $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,gif'])->move($path,$savename);

        if ($info){
            $ext = $info->getExtension();
            $absolute = $path.'\\'.$savename.'.'.$ext;
            $prefix =substr($absolute,0,37);
            $thumb = substr($absolute,37);
            $user = MemberModel::get($role_id);
            $old = $user->getAttr('thumb');
            $user->thumb = $thumb;
            $result = $user->save();
            if ($result){
                unlink($prefix.$old);
                session('thumb',$thumb);
                $this->redirect($role.'/index');
            }
        }

        $info = '上传的图片不符合规范，上传失败';
        $this->redirect($role.'/'.$role.'/thumb',['info'=>$info]);
    }
    public function bookshelf(){
        $title = "我的书架";
        $member_id = session('role_id');
        $bookshelf = Bookshelf::where('member_id',$member_id)->select();

        $this->assign(['title'=>$title,'bookshelf'=>$bookshelf]);
        return $this->fetch();
    }
    //加入书架
    public function addBook(){
        $data['article_id'] = input('article_id/d');
        $data['member_id'] =session('role_id');
        $exits = Bookshelf::where('member_id',$data['member_id'])->where('article_id',$data['article_id'])->select()->toArray();
        if (!$exits){
            $bookshelf = new Bookshelf();
            $result = $bookshelf->allowField(true)->save($data);
            if ($result){
                $info = '添加成功';
               Fav::where('article_id',$data['article_id'])->setInc('favorite');
            }else{
                $info = '未知错误，添加失败';
            }
        }else{
            $info = '已存在，不需重复添加';
        }
        $url = url('book/index/info',['id'=>$data['article_id'],'info'=>$info]);
        $this->redirect($url);
    }
    //从书架中移除
    public function removeBook(){
        $data['article_id'] = input('article_id/d');
        $data['member_id'] = session('role_id');
        $bookshelf = new Bookshelf();
        $bookshelf->startTrans();
        $result = $bookshelf->where($data)->delete();
        if ($result!==false){
            $fav = new Fav();
            $fav->startTrans();
            $result = $fav->where('article_id',$data['article_id'])->setDec('favorite');
            if ($result!==false){
                $bookshelf->commit();
                $fav->commit();
                $this->redirect('member/member/bookshelf');
            }

        }
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
        $page = $noticeList->render();
        $this->assign(['title'=>$title,'noticeList'=>$noticeList,'page'=>$page]);
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
        $page = $infoList->render();
        $this->assign(['title'=>$title,'infoList'=>$infoList,'page'=>$page]);
        return $this->fetch();
    }
    //发表评论
    public function publish(){
        $data['user_id'] = session('role_id');
        $data['article_id'] = input('article_id');
        $url = url('book/index/info','id='.$data['article_id']);
        $data['content'] = input('content','','htmlspecialchars');
        if ($data['content'] == ''){
            $this->redirect($url);
        }
        $data['reply_id'] = input('reply_id');

        $com =new Comment();
        $result = $com->allowField(true)->save($data);
        if ($result){
            $this->redirect($url);
        }

    }
    //删除评论
    public function delComment(){
        $article_id = input('article_id/d');
        $comment_id = input('comment_id/d');
        $reply_id = input('reply_id/d');
        $com = new Comment();
        $com->startTrans();
        $delReply = true;
        if (!$reply_id){
            $delReply = $com->where('reply_id',$comment_id)->delete();
            if ($delReply!==false){
                $delReply = true;
            }
        }
        $delCom = $com->destroy($comment_id,true);
        if ($delCom&&$delReply){
            $com->commit();
        }
        $url = url('book/index/info',['id'=>$article_id]);
        $this->redirect($url);

    }

}