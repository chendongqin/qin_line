<?php
namespace app\teacher\controller;

use base\Teacherbase;
use think\Session;
use think\Db;
class index extends Teacherbase
{
    private $rank = array('初级教师','中级教师','高级教师','特级教师');
    //教师首页
    public function index(){
        $teacher = Session::get('teacher');
        $teacher = isset($teacher[0])?$teacher[0]:$teacher;
        $teacher['rank'] = $this->rank[$teacher['rank']];
        $this->assign('teacher',$teacher);
        $this->assign('ranks',$this->rank);
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
        $user = Session::get('teacher');
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
        Db::name('teacher')->update($user);
        Session::delete('teacher');
        return $this->returnJson('修改成功',1001,true);

    }

    //修改信息
    public function update(){
        $teacher = Session::get('teacher');
        $teacher = isset($teacher[0])?$teacher[0]:$teacher;
        $teacher['teacher_name'] = $this->getParam('teacherName');
        $teacher['sex'] = $this->getParam('sex',$teacher['sex'],'int');
        $teacher['birthday'] = date('Y-m-d',strtotime($this->getParam('birthday',date('Y-m-d'))));
        $teacher['update_at'] = date('YmdHis');
        $res = Db::name('teacher')->update($teacher);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //教师上课课程列表
    public function courses(){
        $teacher = Session::get('teacher');
        $teacher = isset($teacher[0])?$teacher[0]:$teacher;
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        //type =1 显示全部课程，否则显示未结束的课程
        $type = $this->getParam('type',0,'int');
        $where = array(
            'teacher_id'=>$teacher['id']
        );
        if($type){
            $today = date('Ymd');
            $where[ 'end_dae'] = array('>=',$today);
        }
        $courses = Db::name('course')->where($where)->paginate($pageLimit,false,array('page'=>$page))->toArray();
        $this->assign('pager',$courses);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
    }

    //获取该课程的学生   默认排序为10
    public function courseStudents(){
        $teacher = Session::get('teacher');
        $teacher = isset($teacher[0])?$teacher[0]:$teacher;
        $courseId = $this->getParam('id');
        $where = array('teacher_id'=>$teacher['id'],'id'=>$courseId);
        $course = Db::name('course')->where($where)->find();
        if (empty($course))
            return $this->returnJson('课程不存在');
        $page = $this->getParam('page',1,'int');
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $courseIns = Db::name('course_in')->where('course_id',$courseId)->paginate($pageLimit,false,array('page'=>$page))->toArray();
        $students = [];
        foreach ($courseIns as $courseIn){
            $students[$courseIn['user_id']] = Db::name('user')->where('id',$courseIn['user_id'])->find();
        }
        return $this->returnJson('成功',1001,true,$students);
    }


    //上班打卡
    public  function work(){
        $hour = (int)date('His');
        $week = date('w');
        $date = date('Ymd');
        $teacher = Session::get('teacher');
        $teacher = isset($teacher[0])?$teacher[0]:$teacher;
        $where = array('teacher_id'=>$teacher['id'],'create_at'=>$date);
        if($work = Db::name('work')->where($where)->find())
            return $this->returnJson('已打卡，请勿重复打卡');
        $work = $where;
        if($hour >83000){
            $work['is_overdue'] = 1;
        }
        if($week ==0 || $week==6)
            $work['is_week'] = 1;
        $res = Db::name('work')->insert($work);
        if(!$res)
            return $this->returnJson('打卡失败');
        return $this->returnJson('成功',1001,true);
    }


}