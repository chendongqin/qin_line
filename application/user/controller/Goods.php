<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:56
 */
namespace app\user\controller;
use base\Userbase;
use think\Session;
use think\Db;
use ku\Verify;
class Goods extends Userbase{
    public function index(){
        $name = $this->getParam('name','','string');
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        $isDown = $this->getParam('isDown',0,'int');
        $where = ['is_down'=>$isDown];
        if($name){
            $where['goods_name'] = array('like',$name.'%');
        }
        $pager = Db::name('goods')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$pager);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
        return $this->fetch();
    }
}