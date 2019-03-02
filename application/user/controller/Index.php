<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 15:41
 */
namespace app\user\controller;
use think\Config;
use think\Session;
use base\Userbase;
use think\Db;
 class Index extends Userbase{

    public function index(){
        $user = Session::get('user');
        $user = isset($user[0])?$user[0]:$user;
        echo  'hello:'.$user['user_no'];
    }
 }