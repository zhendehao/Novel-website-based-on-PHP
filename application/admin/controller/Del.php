<?php


namespace app\admin\controller;
use app\admin\model\SysInfo;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use app\member\model\Member as MemberModel;
use app\author\model\Author as AuthorModel;

class Del extends Controller
{
    public function del(){
        $data = input('');
        $force = input('force');
        if ($data['role']=='admin'){

            $result = AdminModel::destroy($data['role_id'],$force);

        }elseif ($data['role']=='member'){

            $result = MemberModel::destroy($data['role_id'],$force);

        }elseif ($data['role']=='author'){

            $result = AuthorModel::destroy($data['role_id'],$force);

        }else{
            $info = '未知错误';
            $this->redirect(urldecode($data['return']),['info'=>$info]);
        }

        if($result){
            $info = '删除成功';
            return redirect(urldecode($data['return']),['info'=>$info]);
        }else{
            $info = '未知错误，请刷新！';
            return redirect(urldecode($data['return']),['info'=>$info]);
        }
    }

    public function recover(){
        $data = input('');

        if ($data['role']=='admin'){
            $user = AdminModel::onlyTrashed()->find($data['role_id']);
            $result = $user->restore();

        }elseif ($data['role']=='member'){

            $user = MemberModel::onlyTrashed()->find($data['role_id']);
            $result = $user->restore();

        }elseif ($data['role']=='author'){

            $user = AuthorModel::onlyTrashed()->find($data['role_id']);
            $result = $user->restore();

        }else{
            $info = '未知错误';
            $this->redirect(urldecode($data['return']),['info'=>$info]);
        }

        if($result){
            $info = '成功恢复';
            return redirect(urldecode($data['return']),['info'=>$info]);
        }else{
            $info = '未知错误，请刷新！';
            return redirect(urldecode($data['return']),['info'=>$info]);
        }
    }
    public function noticeDel(){
        $info_id = input('info_id/d');
        $result = SysInfo::destroy($info_id,true);
        if ($result){
            $info = '删除成功';
            $this->redirect('admin/user/manage_notice',['info'=>$info]);
        }
    }


}