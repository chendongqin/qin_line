<?php
namespace app\user\controller;
use app\user\model\User;
use base\Base;
use think\Cache;
use think\Db;
use think\Session;

class Regist extends Base{

    /**用户注册页面
     * @return mixed
     */
    public function index(){
        $owner = Session::get('user');
        $owner = isset($owner[0])?$owner[0]:$owner;
        if(!empty($owner)){
           return $this->redirect('/user');
        }
        return $this->fetch();
    }
    
    /**
     * 用户注册接口
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function i(){
        $owner = Session::get('user');
        $owner = isset($owner[0])?$owner[0]:$owner;
        if(!empty($owner)){
            return $this->returnJson('您已是登陆状态');
        }
        $mobile = $this->getParam('mobile','');
        $password = $this->getParam('password','');
        $sure = $this->getParam('sure','');
        $code = $this->getParam('code','');
        $virefyCode = Cache::get('userReg'.$mobile);
        if($virefyCode != $code){
            return $this->returnJson('手机验证码不正确',1000);
        }
        $model = new User();
        $owner = $model->where(array('mobile'=>$mobile))->find();
        if(empty($mobile) || empty($password) || empty($sure) || empty($code)){
            return $this->returnJson('请填写所有注册信息',1002);
        }
        if(!empty($owner)){
            return $this->returnJson('该手机已注册',1002);
        }
        if($sure !=$password){
            return $this->returnJson('两次密码不一致',1000);
        }
        if(strlen($password) <6 or strlen($password) >30){
            return $this->returnJson('密码长度在6到30位之间',1000);
        }
        $pwd = $this->createPwd($password);
        $today = date('Ymd');
        $count = Db::name('user')->where('create_at >= '.$today.'000000 and create_at <= '.$today.'235959')->count('id');
        $sn = sprintf("%04d", $count+1);
        $model->data(
            array(
                'mobile'=>$mobile,
                'password'=>$pwd,
                'create_at'=>date('YmdHis'),
                'user_no'=>date('YmdH').$sn.mt_rand(10,99),
                )
        );
        $model->save();
        $owner = $model->where(array('mobile'=>$mobile))->find();
        Session::push('user',$owner);
        return $this->returnJson('注册成功',1001,true);
    }

}