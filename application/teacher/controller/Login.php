<?php

namespace app\teacher\controller;
use app\teacher\model\Teacher;
use think\Config;
use think\Session;
use base\Base;
use think\Db;
use think\Cache;
class Login extends Base
{

    public function index(){

        return $this->fetch();
    }


    public function i(){
        $teacher = Session::get('teacher');
        $teacher = isset($teacher[0])?$teacher[0]:$teacher;
        if(!empty($teacher)){
            return $this->returnJson('已登陆',9999);
        }
        $userName = $this->getParam('teacherNo','');
        $password = $this->getParam('password','');
        $code = $this->getParam('code','');
        $loginCode = Cache::get('teacherLogin'.$userName);
        if($loginCode != $code){
            return $this->returnJson('验证码不正确',1000);
        }
        $model = new Teacher();
        $teacher = $model->where('teacher_no',$userName)->find();
        if(empty($teacher)){
            return $this->returnJson('用户不存在');
        }
        if(!$this->virefyPwd($teacher['password'],$password)){
            return $this->returnJson('密码错误');
        }
        Session::push('teacher',$teacher);
        return $this->returnJson('登陆成功',1001,true);
    }


}