<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:48
 */
namespace app\index\controller;
use base\Base;
use ku\Verify;
use ku\Tool;
use think\Session;
use think\Cache;
class Captcha extends Base{

    public function index(){
        $request = $this->request;
        $channel = $request->param('channel','','string');
        Tool::captcha(4,90,32,$channel);
    }
    
    public function show(){
        $channel = $this->getParam('channel','','string');
        $this->assign('channel',$channel);
        return $this->fetch();
    }


    /**短信发送
     * channel=ownerLogin,ownerReg,userLogin,userReg,//短信发送渠道
     * @return \think\response\Json
     */
    public function sms(){
        $channel = $this->getParam('channel','');
        $mobile = $this->getParam('mobile','');
        $code = $this->getParam('code','');
        if(!Verify::isMobile($mobile)){
            return $this->returnJson('手机号不正确',1000);
        }
        $virefyCode = Session::get($channel.'_virefy_code');
        if($code != strtolower($virefyCode)){
            return $this->returnJson('验证码错误',1000);
        }
        $sendVirefy = Cache::get($channel.'send'.$mobile);
        if($sendVirefy){
            return $this->returnJson('发送太频繁',1000);
        }
        $mobileCode = Tool::randCode(4,false,false);
        $mobileCode = '0000';
        Cache::set($channel.$mobile,$mobileCode,300);
        Cache::set($channel.'send'.$mobile,1,60);
        return $this->returnJson('发送成功',1001,true);
    }

}