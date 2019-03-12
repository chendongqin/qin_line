<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:56
 */
namespace app\teacher\controller;

use base\Teacherbase;
use think\Session;
class Logout extends Teacherbase {

    //退出登陆
    public function index(){
        $sission = new Session();
        $sission->delete('teacher');
        return $this->redirect('/teacher/login');
    }
}