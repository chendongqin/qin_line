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
class Goods extends Adminbase{
    private $orderStatus = array('待支付','已支付','已收货','申请退货','退货成功','退货失败');
    public function index(){
        $name = $this->getParam('name','','string');
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        $isDown = $this->getParam('isDown',0,'int');
        $where = ['is_down'=>$isDown];
        if($name){
            $where['goods_name'] = array('like',$name.'%');
        }
        $pager = Db::name('course')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$pager);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
        return $this->fetch();
    }

    //添加
    public function add(){
        $goods = [];
        $goods['goods_name'] = $this->getParam('name','','string');
        if(empty($goods['goods_name']))
            return $this->returnJson('商品名称不能为空');
        $goods['stock'] = $this->getParam('stock',0,'int');
        $goods['is_down'] = $this->getParam('isDown',0,'int');
        $goods['price'] = $this->getParam('price');
        if(!is_numeric($goods['price']))
            return $this->returnJson('价格格式错误');
        $goods['describe'] = $this->getParam('describe');
        $res = Db::name('goods')->insert($goods);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //下架
    public function down(){
        $id = $this->getParam('id');
        if(!$goods = Db::name('goods')->where(array('id'=>$id))->find()){
            return $this->returnJson('商品不存在');
        }
        $goods['is_down'] = 1;
        $res = Db::name('goods')->update($goods);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //信息修改
    public function update(){
        $id = $this->getParam('id');
        if(!$goods = Db::name('goods')->where(array('id'=>$id))->find()){
            return $this->returnJson('用户不存在');
        }
        $goods['goods_name'] = $this->getParam('name','','string');
        if(empty($goods['goods_name']))
            return $this->returnJson('商品名称不能为空');
        $goods['stock'] = $this->getParam('stock',0,'int');
        $goods['is_down'] = $this->getParam('isDown',0,'int');
        $goods['price'] = $this->getParam('price');
        if(!is_numeric($goods['price']))
            return $this->returnJson('价格格式错误');
        $goods['describe'] = $this->getParam('describe');
        $res = Db::name('course')->update($goods);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //商品订单
    public function orders(){
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        $status = $this->getParam('status',100,'int');
        $where = [];
        if($status !=100){
            $where['status'] = $status;
        }
        $pager = Db::name('goods')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$pager);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
        $this->assign('orderStatus',$this->orderStatus);
        return $this->fetch();
    }




}