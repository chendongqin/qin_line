<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:56
 */
namespace app\admin\controller;
use base\Adminbase;
use think\Session;
use think\Db;
use ku\Verify;
class Data extends Adminbase{
    private $orderStatus = array('待支付','已支付','已收货','申请退货','退货成功','退货失败');


    public function index(){
        return $this->fetch();
    }







}