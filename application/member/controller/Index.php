<?php


namespace app\member\controller;
use think\Controller;
use app\admin\validate\Register as RegValidate;
use app\member\model\Member as MemberModel;

class Index extends Controller
{
    public function login(){
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $this->assign('info',$info);
        return $this->fetch();

    }
    public function check(){
        $data = input('post.');
        if (!captcha_check($data['code'])){
            $info = '验证码不正确';
            $this->redirect('Index/login',['info'=>$info]);
        }
        $user = new MemberModel();
        $result = $user->where('role_id',$data['role_id'])->find();

        if($result){
            if ($result['password']===substr(md5($data['password']),1,30)){
                session('role','member');
                session('role_id',$result['role_id']);
                session('nickname',$result['nickname']);
                session('thumb',$result['thumb']);
                session('sign',$result['sign']);
                $this->redirect('member/member/index');
            }else{
                $info = '密码不正确';
                $this->redirect('Index/login',['info'=>$info]);
            }
        }else{
            $info = '用户不存在';
            $this->redirect('Index/login',['info'=>$info]);
        }
    }

    public function logout(){
        session(null);
        return redirect('index/login');
    }

    public function register(){
        if (input('?info')){
            $info = input('info');
        }else{
            $info = '';
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function checkReg()
    {
        $data = input('');
        if (!captcha_check($data['code'])){
            $this->redirect('author/register',['info'=>'验证码错误']);
        }
        $val = new RegValidate();
        if (!$val->check($data)) {
            $info = $val->getError();
            $this->redirect('index/register', ['info' => $info]);
        }
        $user = new MemberModel();
        $ret = $user->allowField(true)->save($data);
        $role_id = $user->getAttr('role_id');
        if ($ret) {
            $info = '恭喜您注册成功您的账号为：' . $role_id;
            $this->redirect('index/login', ['info' => $info]);
        } else {
            $this->redirect('index/register', ['info' => '未知错误，注册失败']);
        }
    }

}