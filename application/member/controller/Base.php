<?php


namespace app\member\controller;
use think\Controller;
use think\Request;

class Base extends Controller
{
    //用来存放需要用户登录之后才能操作的方法的集合
    protected $is_check_login = ['*'];

    public function _initialize()
    {
        if(!$this->isLogin() && (in_array(Request::instance()->action(),$this->is_check_login) || $this->is_check_login[0] == '*')){

            $this->redirect('member/Index/login');
        }
    }

    public function isLogin(){
        $role = session('role');
        if ($role == 'admin'||$role == 'member'){
            return true;
        }
        return false;
    }



}