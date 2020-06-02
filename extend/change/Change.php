<?php
namespace change;
use app\admin\model\Admin as AdminModel;
use app\member\model\Member as MemberModel;
use app\author\model\Author as AuthorModel;
use page\Page;


class Change
{
    public function alterNickname($role,$role_id,$nickname=null){

        switch ($role){
            case 'admin':
                $user = AdminModel::get($role_id);
                break;
            case 'member':
                $user = MemberModel::get($role_id);
                break;
            case 'author':
                $user = AuthorModel::get($role_id);
                break;
            default:
                $user = null;
        }
        if (!is_null($user)){
            if (!is_null($nickname)){
                $user->nickname = $nickname;
                $result = $user->save();
                if ($result)
                    return true;
            }
        }
        return false;
    }

    public function alterPassword($role,$role_id,$oldPass,$password=null){
        switch ($role){
            case 'admin':
                $user = AdminModel::get($role_id);
                break;
            case 'member':
                $user = MemberModel::get($role_id);
                break;
            case 'author':
                $user = AuthorModel::get($role_id);
                break;
            default:
                $user = null;
        }
        if (!is_null($user)){
            if ($user['password']===substr(md5($oldPass),1,30))
                if(!is_null($password)){
                    $user->password = $password;
                    $result = $user->save();
                    if ($result)
                        return true;
                }
        }
        return false;

    }
    public function alterSign($role,$role_id,$sign){
        switch ($role){
            case 'member':
                $user = MemberModel::get($role_id);
                break;
            case 'author':
                $user = AuthorModel::get($role_id);
                break;
            default:
                $user = null;
        }
        if (!is_null($user)){
            $user->sign = $sign;
            $result = $user->save();
            if ($result){
                return true;
            }
        }
        return false;
    }
    public function alterMail($role,$role_id,$mail){
        switch ($role){
            case 'member':
                $user = MemberModel::get($role_id);
                break;
            case 'author':
                $user = AuthorModel::get($role_id);
                break;
            default:
                $user = null;
        }
        if (!is_null($user)){
            $user->mail = $mail;
            $result = $user->save();
            if ($result){
                return true;
            }
        }
        return false;
    }
    public function alterThumb($role,$role_id,$thumb){
        switch ($role){
            case 'member':
                $user = MemberModel::get($role_id);
                break;
            case 'author':
                $user = AuthorModel::get($role_id);
                break;
            default:
                $user = null;
        }
        if (!is_null($user)){
            $user->thumb = $thumb;
            $result = $user->save();
            if ($result)
                return true;
        }
        return false;
    }

    public function initPass($role,$role_id){
        switch ($role){
            case 'admin':
                $user = AdminModel::get($role_id);
                $user->password ='abc123';
                break;
            case 'member':
                $user = MemberModel::get($role_id);
                $user->password = 123456;
                break;
            case 'author':
                $user = AuthorModel::get($role_id);
                $user->password = 123456;
                break;
            default:
                $user = null;
        }
//        if ($role =='admin'){
//            $user = AdminModel::get($role_id);
//            $user->password ='abc123';
//        }elseif ($role=='member'){
//            $user = MemberModel::get($role_id);
//            $user->password = 123456;
//        }elseif ($role=='author'){
//            $user = AuthorModel::get($role_id);
//            $user->password = 123456;
//        }else{
//            return false;
//        }
        if (!is_null($user)){
            $result = $user->save();
            if($result) {
                return true;
            }
        }
        return false;

    }
}