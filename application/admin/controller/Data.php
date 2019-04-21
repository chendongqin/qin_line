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


    //数据预览
    public function index(){
        //商品总库存
        $data['goodsTotal'] = Db::name('goods')->where('is_down',0)->sum('stock');
        //总出售金额与出售商品数量
        $data['totalMoney'] = Db::name('goods_order')->where('status in(1,2)')->sum('amount');
        $data['totalNum'] = Db::name('goods_order')->where('status in(1,2)')->sum('buy_num');
        //前三销售商品
        $data['goodsTop3'] = Db::query("select goods_id,sum(`buy_num`)as allBuy from ql_goods_order group by `goods_id` limit 0,3");
        foreach ($data['goodsTop3'] as $key=>$datum){
            $goods = Db::name('goods')->where('id',$datum['goods_id']);
            $data['goodsTop3'][$key]['goods']=$goods;
            unset($data['goodsTop3'][$key]['goods_id']);
        }
        //前五课程
        $data['courseTop5'] = Db::name('course')->order('people')->limit(5);
        foreach ($data['courseTop5'] as $key=>$datum){
            $teacher = Db::name('teacher')->where('id',$datum['teacher_id']);
            unset($teacher['password']);
            $data['courseTop5'][$key]['teacher']=$teacher;
        }
        foreach ($data as $key=>$value){
            $this->assign($key,$value);
        }
        return $this->fetch();
    }

    //教师工资条
    public function salary(){
        //工资查询时间（默认上个月）
        $month = $this->getParam('month',date('Y-m',strtotime('-1 month')));
        $pageLimit = $this->getParam('pageLimit',10,'int');
        $page = $this->getParam('page',1,'int');
        $name = $this->getParam('name','','string');
        $where = [];
        if($name){
            $where['teacher_name'] = array('like',$name.'%');
        }
        $pager = Db::name('course')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        $data = $pager['data'];
        $allWorkDays = $this->workDays($month,true);
        //月初
        $begin = date('Ymd',strtotime($month.'-01'));
        //月尾
        $end = date('Ymd',strtotime('-1 day',strtotime('+1 month',strtotime($begin))));
        foreach ($data as $key =>$value){
            //正常打卡
            $count = Db::name('work')->where('create_at >= '.$begin .' and create_at <='.$end .'and is_week=0')->count();
            $data[$key]['workDays'] = $count;
            //完整加班天数
            $isweek = Db::name('work')->where('create_at >= '.$begin .' and create_at <='.$end .'and is_week=1 and is_overdue=0')->count();
            $data[$key]['isWeekDays'] = $isweek;
            //迟到天数
            $overdue = Db::name('work')->where('create_at >= '.$begin .' and create_at <='.$end .'and is_week=0 and is_overdue=1')->count();
            $data[$key]['overdueDays'] = $overdue;
            //迟到加班天数算0.5天的工资
            $overdueOfIsWeek = Db::name('work')->where('create_at >= '.$begin .' and create_at <='.$end .'and is_week=1 and is_overdue=1')->count();
            $data[$key]['overdueOfIsWeekDays'] = $overdueOfIsWeek;
            //加班补贴
            $data[$key]['addSalary'] = number_format(($isweek+0.5*$overdueOfIsWeek)/$allWorkDays*$value['salary']*1.5,2,'.','');
            //正常工资
            $data[$key]['commonSalary'] = number_format($count/$allWorkDays*$value['salary'],2,'.','');
            //迟到扣款
            $data[$key]['overdueSalary'] = number_format($overdue/$allWorkDays*$value['salary']*0.3,2,'.','');
            //总工资
            $data[$key]['monthSalary'] = number_format($data[$key]['commonSalary']+$data[$key]['addSalary']-$data[$key]['overdueSalary'],2,'.','');
        }
        $pager['data'] = $data;
        $this->assign('pager',$pager);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
        var_dump($pager);
        return $this->fetch();
    }


//    public function salarydetail(){
//        $teacherId = $this->getParam('id');
//        $month = $this->getParam('month',date('Y-m',strtotime('-1 month')));
//        //月初
//        $begin = date('Ymd',strtotime($month.'-01'));
//        //月尾
//        $end = date('Ymd',strtotime('-1 day',strtotime('+1 month',strtotime($begin))));
//        $workes = Db::name('work')->where('create_at >= '.$begin .' and create_at <='.$end)->order('id','asc')->select();
//    }



}