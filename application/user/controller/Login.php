<?php
namespace app\user\controller;
use app\user\model\User;
use base\Base;
use ku\Tool;
use ku\Verify;
use think\Cache;
use think\Session;

class login extends Base{

    public function index(){
        return $this->fetch();
    }

    public function i(){
        $owner = Session::get('user');
        $owner = isset($owner[0])?$owner[0]:$owner;
        if(!empty($owner)){
            return $this->returnJson('已登陆',9999);
        }
        $username = $this->getParam('username','');
        $password = $this->getParam('password','');
        $code = $this->getParam('code','');
        $loginCode = Session::get('userLogin_virefy_code');
        if($loginCode != $code){
            return $this->returnJson('验证码不正确',1000);
        }
        $model = new User();
        if(Verify::isMobile($username)){
            $owner = $model->where(array('mobile'=>$username))->find();
        }else{
            $owner = $model->where(array('user_no'=>$username))->find();
        }
        if(empty($owner)){
            return $this->returnJson('用户名不存在',1000);
        }
        if(!$this->virefyPwd($owner['password'],$password)){
            return $this->returnJson('密码不正确',1000);
        }
        Session::push('user',$owner);
        return $this->returnJson('登陆成功',1001,true);
    }

}
