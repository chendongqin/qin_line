<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 15:41
 */
namespace app\index\controller;
use think\Config;
use think\Session;
use base\Base;
use think\Db;
 class Index extends Base{

     /**首页数据
      * @return mixed
      * @throws \think\db\exception\DataNotFoundException
      * @throws \think\db\exception\ModelNotFoundException
      * @throws \think\exception\DbException
      */
     public function index(){
         $where = ['is_down'=>0];
         $name = $this->getParam('name');
         if($name){
             $where['goods_name'] = array('like',$name.'%');
         }
         $pageLimit = $this->getParam('pageLimit',15,'int');
         $page = $this->getParam('page',1,'int');
         $pager = Db::name('goods')->where($where)->paginate($pageLimit,false,array('page'=>$page))->toArray();
         $this->assign('pager',$pager);
         $this->assign('page',$page);
         $this->assign('pageLimit',$pageLimit);
         return $this->fetch();
     }

     public function course(){
         $today = date('Ymd');
         $where = ['end_date'=>array('>=',$today)];
         $name = $this->getParam('name');
         if($name){
             $where['course_name'] = array('like',$name.'%');
         }
         $pageLimit = $this->getParam('pageLimit',15,'int');
         $page = $this->getParam('page',1,'int');
         $pager = Db::name('goods')->where($where)->paginate($pageLimit,false,array('page'=>$page))->toArray();
         $this->assign('pager',$pager);
         $this->assign('page',$page);
         $this->assign('pageLimit',$pageLimit);
         return $this->fetch();
     }


//     public function test(){
//        $teachers = Db::name('teacher')->where(['isdel'=>0])->select();
//        foreach ($teachers as $teacher){
//            $begin = strtotime('2019-03-01');
//            $end  = strtotime(date('Y-m-d'));
//            while($begin <= $end){
//                $rand = rand(1,5);
//                $week = date('w',$begin);
//                if($rand != 1 and ($week == 0 or $week == 6)){
//                    goto addDate;
//                }
//                $where = array('teacher_id'=>$teacher['id'],'create_at'=>date('Ymd',$begin));
//                if($work = Db::name('work')->where($where)->find()){
//                    goto addDate;
//                }
//                $work = $where;
//                $rand = rand(11,30);
//                if($rand == 11){
//                    $work['is_overdue'] = 1;
//                }
//                if($week ==0 || $week==6){
//                    $work['is_week'] = 1;
//                }
//                Db::name('work')->insert($work);
//                addDate:
//                $begin = strtotime('+1 day',$begin);
//            }
//        }
//
//
//     }

 }