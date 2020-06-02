<?php


namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use think\Request;

class Index extends Controller
{
    public function login(){

        if (input('?status')){
            switch (input('status')){
                case 1:
                    $info = '密码错误';
                    break;
                case 2:
                    $info = '账号不存在';
                    break;
                case 3:
                    $info = '验证码不正确';
                    break;
                default:
                    $info = '';
                    break;
            }
        }else {
            $info = '';
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function check(){
        $data = input('post.');
        if (!captcha_check($data['code'])){
            $this->redirect('Index/login',array('status'=>3));
        }
        $user = new AdminModel();
        $result = $user->where('nickname',$data['nickname'])->find();

        if($result){
            if ($result['password']===substr(md5($data['password']),1,30)){
                session('role','admin');
                session('role_id',$result['role_id']);
                session('nickname',$result['nickname']);
                session('level',$result['level']);

                $this->redirect('User/index');
            }else{
                $this->redirect('Index/login',array('status'=>1));
            }
        }else{
            $this->redirect('Index/login',array('status'=>2));
        }
    }

    public function logout(){
        session(null);
        return redirect('index/login');
    }
}