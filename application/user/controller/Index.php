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

     private $orderStatus = array('待支付','已支付','已收货','申请退货','退货成功','退货失败');

    public function index(){
        $user = Session::get('teacher');
        $user = isset($user[0])?$user[0]:$user;
        $this->assign('user',$user);
        return $this->fetch();
    }

     /**
      * 修改密码
      * @return \think\response\Json
      * @throws \think\Exception
      * @throws \think\exception\PDOException
      */
     public function changepw()
     {
         $pwd = $this->getParam('pwd','','string');
         $newpwd = $this->getParam('newpwd','','string');
         $sure = $this->getParam('sure','','string');
         $user = Session::get('user');
         $user = $user[0];
         if(!$this->virefyPwd($user['password'],$pwd)){
             return $this->returnJson('原密码不正确');
         }
         if($newpwd != $sure){
             return $this->returnJson('两次密码不一致');
         }
         if(strlen($newpwd)<6){
             return $this->returnJson('密码长度需要大于6位');
         }
         $user['password'] = $this->createPwd($newpwd);
         Db::name('user')->update($user);
         Session::delete('user');
         return $this->returnJson('修改成功',1001,true);

     }

    //修改信息
    public function update(){
        $user = Session::get('teacher');
        $user = isset($user[0])?$user[0]:$user;
        $user['user_name'] = $this->getParam('userName');
        $user['sex'] = $this->getParam('sex',$user['sex'],'int');
        $user['birthday'] = date('Y-m-d',strtotime($this->getParam('birthday',date('Y-m-d'))));
        $user['update_at'] = date('YmdHis');
        $res = Db::name('user')->update($user);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //用户课程
    public function mycourse(){
        $user = Session::get('user');
        $user = isset($user[0])?$user[0]:$user;
        $where = array('user_id',$user['id']);
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        $courses = [];
        $pager = Db::name('course_in')->where($where)->order('id','desc')->paginate($pageLimit,false,array('page'=>$page))->toArray();
        foreach ($pager['data'] as $key=>$datum){
            $course = Db::name('course')->where('id',$datum['course_id'])->find();
            $teacher = Db::name('teacher')->where('id',$course['teacher_id'])->find();
            $course['teacher_name'] = $teacher['teacher_name'];
            $courses[$key] = $course;
        }
        $pager['data'] = $courses;
        $this->assign('pager',$pager);
        $this->assign('page',$page);
        $this->assign('pageLimit',$pageLimit);
        return $this->fetch();
    }

    //用户订单
    public function myorders()
    {
        $user = Session::get('user');
        $user = isset($user[0])?$user[0]:$user;
        $where = array('user_id',$user['id']);
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        $status = $this->getParam('status',100,'int');
        if($status != 100)
            $where['status'] = $status;
        $pager = Db::name('goods_order')
            ->where($where)->
            order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            //对象转数组
            ->toArray();
        $data = [];
        foreach ($pager['data'] as $key =>$item){
            $data[$key] = $item;
            $goods = Db::name('goods')->where('id',$item['goods_id'])->find();
            $data[$key]['goods_name'] = $goods['goods_name'];
            $data[$key]['goods_describe'] = $goods['describe'];
        }
        $this->assign('pager',$pager);
        $this->assign('page',$page);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('orderStatus',$this->orderStatus);
        return $this->fetch();
    }

    //上课打卡
     public  function work(){
         $user = Session::get('teacher');
         $user = isset($user[0])?$user[0]:$user;
         $courseId = $this->getParam('courseId');
         if(!$course = Db::name('course')->where('id',$courseId)->find()){
             return $this->returnJson('课程不存在');
         }
         $date = date('Ymd');
         if($course['end_date'] < $date){
             return $this->returnJson('课程已结束');
         }
         if(!$courseId = Db::name('course_in')->where(array('user_id'=>$user['id'],'course_id'=>$courseId))->find()){
             return $this->returnJson('你没有报名此课程');
         }
         $hour = date('H:i');
         $where = array('user_id'=>$user['id'],'class_times'=>$course['long_time'],'course_id'=>$courseId);
         if($work = Db::name('course_work')->where($where)->find())
             return $this->returnJson('已打卡，请勿重复打卡');
         $work = $where;
         if($hour >$course['begin_at']){
             $work['is_overdue'] = 1;
         }
         $work['time'] = $date;
         $res = Db::name('course_work')->insert($work);
         if(!$res)
             return $this->returnJson('打卡失败');
         return $this->returnJson('成功',1001,true);
     }


     //用户报名上课
     public function attend()
     {
         $user = Session::get('teacher');
         $user = isset($user[0])?$user[0]:$user;
         $courseId = $this->getParam('courseId');
         if(!$course = Db::name('course')->where(array('id'=>$courseId))->find())
             return $this->returnJson('课程不存在');
         if($courseIn = Db::name('course_in')->where(array('user_id'=>$user['id'],'course_id'=>$courseId))){
             return $this->returnJson('该用户已报名该课程');
         }
         $today = date('Ymd');
         if($course['begin_date']<$today)
             return $this->returnJson('课程已开始');
         if($user['balance'] < $course['fee']){
             return $this->returnJson('用户余额不足');
         }
         if($course['people']+1 > $course['max_people']){
             return $this->returnJson('超过人数限制');
         }
         $add = array(
             'user_id'=>$user['id'],
             'course_id'=>$course['id'],
             'create_at'=>date('YmdHis')
         );
         Db::startTrans();
         $res = Db::name('course_in')->insert($add);
         if(!$res){
             Db::rollback();
             return $this->returnJson('失败');
         }
         $user['balance'] = 'balance-'.$course['fee'];
         $user['update_at'] = date('YmdHis');
         $res = Db::name('user')->update($user);
         if(!$res){
             Db::rollback();
             return $this->returnJson('失败');
         }
         $course['people'] = 'people+1';
         $res = Db::name('course')->update($course);
         if(!$res){
             Db::rollback();
             return $this->returnJson('失败');
         }
         Db::commit();
         return $this->returnJson('成功',1001,true);
     }

    //购买商品
     public function buy(){
         $goodsId = $this->getParam('goodsId',0,'int');
         $goodsNum = $this->getParam('goodsNum',1,'int');
         if($goodsNum<=0)
             return $this->returnJson('购买数量必须大于1');
         if($goods = Db::name('goods')->where(array('id'=>$goodsId,'is_down'=>0))->find()){
             return $this->returnJson('商品不存在');
         }
         if($goods['stock'] <$goodsNum)
             return $this->returnJson('库存不足');
         $user = Session::get('teacher');
         $user = isset($user[0])?$user[0]:$user;
         $amount = number_format($goods['price'] * $goodsNum * $goods['discount'] /100,2,'.','');
         if($user['balance'] <= $amount)
             return $this->returnJson('用户账号月不足');
         $address = $this->getParam('address');
         if(!$address)
             return $this->returnJson('收货地址不能为空');
         $order = array(
             'goods_id'=>$goodsId,
             'user_id'=>$user['id'],
             'amount'=>$amount,
             'buy_num'=>$goodsNum,
             'discount'=>$goods['discount'],
             'status'=>1,
             'create_at'=>date('YmdHis'),
             'address'=>$address,
         );
         Db::startTrans();
         $user['balance'] = 'balance-'.$amount;
         $user['update_at'] = date('YmdHis');
         $res = Db::name('user')->update($user);
         if (!$res){
             Db::rollback();
             return $this->returnJson('失败');
         }
         $res = Db::name('goods_order')->insert($order);
         if (!$res){
             Db::rollback();
             return $this->returnJson('失败');
         }
         Db::commit();
         return $this->returnJson('成功',1001,true);
     }

     //用户充值模拟,只有卡号前缀为“C350822”才充值成功
     public function recharge(){
         $user = Session::get('teacher');
         $user = isset($user[0])?$user[0]:$user;
         $cardNo = $this->getParam('cardNo');
         if(strpos($cardNo,'C350822') === 0){
            $amount = $this->getParam('amount');
            if(!is_numeric($amount) || $amount <0){
                return $this->returnJson('金额格式错误');
            }
             $user['balance'] = 'balance+'.$amount;
             $res = Db::name('user')->update($user);
             if(!$res)
                 return $this->returnJson('失败');
             return $this->returnJson('成功',1001,true);
         }
         return $this->returnJson('充值失败,卡号不正确');
     }

     //取消订单
     public function cancelOrder(){
         $user = Session::get('user');
         $user = isset($user[0])?$user[0]:$user;
         $orderId = $this->getParam('orderId');
         $where = array(
             'id'=> $orderId,
             'user_id'=>$user['id'],
             'status'=>1
         );
         if(!$order = Db::name('goods_order')->where($where)->find())
             return $this->returnJson('该订单无法退货');
         $order['status'] = 3;
         $order['update_at'] = date('YmdHis');
         $res = Db::name('goods_order')->update($order);
         if($res)
             return $this->returnJson('成功',1001,true);
         return $this->returnJson('失败');
     }

 }