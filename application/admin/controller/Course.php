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
class Course extends Adminbase{

    private $rank = array('初级教师','中级教师','高级教师','特级教师');
    public function index(){
        $name = $this->getParam('name','','string');
        $roomsName = $this->getParam('roomsName','','string');
        $teacherId = $this->getParam('teacherId','','int');
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        $where = ['isdel'=>0];
        if($name){
            $where['course_name'] = array('like',$name.'%');
        }
        if($roomsName){
            $where['rooms'] = array('like',$roomsName.'%');
        }
        if($teacherId){
            $where['teacher_id'] = $teacherId;
        }
        $pager = Db::name('course')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        foreach ($pager['data'] as $key =>$value){
            $teacher = Db::name('teacher')->where('id',$value['teacher_id'])->find();
            $pager['data'][$key]['teacher'] = array(
                'teacher_name' =>$teacher['teacher_name'],
                'rank'=>$this->rank[$teacher['rank']]
            );
        }
        $this->assign('pager',$pager);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
        return $this->fetch();
    }

    //添加
    public function add(){
        $course = [];
        $course['teacher_id'] = $this->getParam('teacherId',0,'int');
        if(!$teacher = Db::name('teacher')->where(array('id'=>$course['teacher_id'],'isdel'=>0))->find())
            return $this->returnJson('教师不存在');
        $course['course_name'] = $this->getParam('courseName');
        $course['rooms'] = $this->getParam('roomsName');
        $course['begin_date'] = date('Y-m-d',strtotime($this->getParam('beginDate')));
        $course['end_date'] = date('Y-m-d',strtotime($this->getParam('endDate')));
        if($course['begin_date'] < date('Y-m-d'))
            return $this->returnJson('开始时间不能小于当前时间');
        if($course['begin_date'] >$course['end_date'])
            return $this->returnJson('开始时间不能大于结束时间');
        $course['begin_at'] = $this->getParam('begin_at');
        $course['end_at'] = $this->getParam('end_at');
        $course['max_people'] = $this->getParam('maxPeople',100,'int');
        $course['fee'] = $this->getParam('fee');
        if(!is_numeric($course['fee']))
            return $this->returnJson('费用格式不正确');
        $res = Db::name('course')->insert($course);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //删除
    public function del(){
        $id = $this->getParam('id');
        if(!$course = Db::name('course')->where(array('id'=>$id))->find()){
            return $this->returnJson('用户不存在');
        }
        if($course['people']>0)
            return $this->returnJson('已有学生报名，无法删除');
        $res = Db::name('course')->delete(array('id'=>$id));
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //信息修改
    public function update(){
        $id = $this->getParam('id');
        if(!$course = Db::name('course')->where(array('id'=>$id))->find()){
            return $this->returnJson('用户不存在');
        }
        if($course['people'] >0)
            return $this->returnJson('开始报名的课程无法修改');
        $course['course_name'] = $this->getParam('courseName');
        $course['rooms'] = $this->getParam('roomsName');
        $course['begin_date'] = date('Y-m-d',strtotime($this->getParam('beginDate')));
        $course['end_date'] = date('Y-m-d',strtotime($this->getParam('endDate')));
        if($course['begin_date'] < date('Y-m-d'))
            return $this->returnJson('开始时间不能小于当前时间');
        if($course['begin_date'] >$course['end_date'])
            return $this->returnJson('开始时间不能大于结束时间');
        $course['begin_at'] = $this->getParam('begin_at');
        $course['end_at'] = $this->getParam('end_at');
        $course['max_people'] = $this->getParam('maxPeople',100,'int');
        $course['teacher_id'] = $this->getParam('teacherId',0,'int');
        if(!$teacher = Db::name('teacher')->where(array('id'=>$course['teacher_id'],'isdel'=>0))->find())
            return $this->returnJson('教师不存在');
        $course['fee'] = $this->getParam('fee');
        if(!is_numeric($course['fee']))
            return $this->returnJson('费用格式不正确');
        $res = Db::name('course')->update($course);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //获取课程信息
    public function getcourse()
    {
        $id = $this->getParam('id');
        $data = Db::name('course')->where('id',$id)->find();
        return $this->returnJson('成功',1001,true,$data);
    }


}