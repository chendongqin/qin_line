<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:56
 */
namespace app\admin\controller;
use base\Adminbase;
use ku\Verify;
use think\Session;
use think\Db;
class Teacher extends Adminbase{

    private $rank = array('初级教师','中级教师','高级教师','特级教师');

    //列表
    public function index(){
        $name = $this->getParam('name','','string');
        $mobile = $this->getParam('mobile','','string');
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page',1,'int');
        $rank = $this->getParam('rank',100,'int');
        $where = ['isdel'=>0];
        if($name){
            $where['teacher_name|teacher_no'] = array('like',$name.'%');
        }
        if($mobile){
            $where['mobile'] = $mobile;
        }
        if($rank != 100){
            $where['rank'] = $rank;
        }
        $admins = Db::name('teacher')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$admins);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
        $this->assign('rank',$this->rank);
        return $this->fetch();
    }
    //添加
    public function add(){
        $data = [];
        $data['teacher_name'] = $this->getParam('teacherName');
        $data['mobile'] = $this->getParam('mobile');
        if(!Verify::isMobile($data['mobile'])){
            return $this->returnJson('手机号格式不正确');
        }
        $data['salary'] = $this->getParam('salary');
        $data['rank'] = $this->getParam('rank',0,'int');
        if(!in_array($data['rank'],array(0,1,2,3)))
            return $this->returnJson('教师等级无效');
        if(!is_numeric($data['salary'])){
            return $this->returnJson('工资格式不正确');
        }
        $teacher['sex'] = $this->getParam('sex',0,'int');
        $pwd = $this->getParam('password');
        if(strlen($pwd)<6){
            return $this->returnJson('密码长度大于6位');
        }
        $data['password'] = $this->createPwd($pwd);
        $data['birthday'] = date('Y-m-d',strtotime($this->getParam('birthday',date('Y-m-d'))));
        $data['teacher_no'] = date('YmdH').mt_rand(1000,9999);
        $data['create_at'] = date('YmdHis');
        $data['update_at'] = date('YmdHis');
        $res = Db::name('teacher')->insert($data);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //删除
    public function del(){
        $id = $this->getParam('id');
        if(!$teacher = Db::name('teacher')->where(array('id'=>$id,'isdel'=>0))->find()){
            return $this->returnJson('教师不存在');
        }
        $teacher['isdel'] = 1;
        $teacher['update_at'] = date('YmdHis');
        $res = Db::name('teacher')->update($teacher);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //信息修改
    public function update(){
        $id = $this->getParam('id');
        if(!$teacher = Db::name('teacher')->where(array('id'=>$id,'isdel'=>0))->find()){
            return $this->returnJson('教师不存在');
        }
        $teacher['teacher_name'] = $this->getParam('teacherName');
        $teacher['mobile'] = $this->getParam('mobile');
        if(!Verify::isMobile($teacher['mobile'])){
            return $this->returnJson('手机号格式不正确');
        }
        $teacher['salary'] = $this->getParam('salary');
        $teacher['rank'] = $this->getParam('rank',0,'int');
        if(!in_array($teacher['rank'],array(0,1,2,3)))
            return $this->returnJson('教师等级无效');
        if(!is_numeric($teacher['salary'])){
            return $this->returnJson('工资格式不正确');
        }
        $teacher['birthday'] = date('Y-m-d',strtotime($this->getParam('birthday',date('Y-m-d'))));
        $teacher['sex'] = $this->getParam('sex',$teacher['sex'],'int');
        $teacher['update_at'] = date('YmdHis');
        $res = Db::name('teacher')->update($teacher);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //密码重置
    public function unsetpwd(){
        $id = $this->getParam('id');
        if(!$teacher = Db::name('teacher')->where(array('id'=>$id,'isdel'=>0))->find()){
            return $this->returnJson('教师不存在');
        }
        $pwd = $this->getParam('password');
        if(strlen($pwd)<6){
            return $this->returnJson('密码长度大于6位');
        }
        $teacher['password'] = $this->createPwd($pwd);
        $teacher['update_at'] = date('YmdHis');
        $res = Db::name('teacher')->update($teacher);
        if(!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功',1001,true);
    }

    //打卡详情,双休
    public function workdetail(){
        $id = $this->getParam('id');
        $pageLimit = $this->getParam('pageLimit',15,'int');
        $page = $this->getParam('page','','int');
        $month = $this->getParam('month',date('Ym'));
        if(!$teacher = Db::name('teacher')->where(array('id'=>$id,'isdel'=>0))->find()){
            return $this->returnJson('教师不存在');
        }
        $where = array('teacher_id'=>$id,'is_week'=>0);
        $where['create_at'] = array('>=',$month.'01');
        $lastMonth = date('Ymd',strtotime('+1 month',strtotime($month.'01')));
        $where['create_at'] = array('<',$lastMonth);
        //本月打卡总天数
        $monthWorkDays = $this->workDays(date('Y-m',strtotime($month.'01')),true);
        $pager = Db::name('work')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$pager);
        $this->assign('pageLimit',$pageLimit);
        $this->assign('page',$page);
        $this->assign('monthWorkDays',$monthWorkDays);
        $this->assign('teacherId',$id);
        return $this->fetch();
    }

    /**
     * 获取教师数据
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getTeacher(){
        $name = $this->getParam('teacherName');
        $pageLimit = $this->getParam('pageLimit',10,'int');
        $page = $this->getParam('page',1,'int');
        $where = array(
            'isdel'=>0,
            'teacher_name'=>array('like',$name.'%')
        );
        $pager = Db::name('teacher')
            ->where($where)
            ->order('id','desc')
            ->paginate($pageLimit,false,array('page'=>$page))
            ->toArray();
        $data = [];
        foreach ($pager['data'] as $item){
            $data[$item['id']] = array(
                'id'=>$item['id'],
                'teacher_name'=>$item['teacher_name'],
                'teacher_no'=>$item['teacher_no'],
                'rank'=>$this->rank[$item['rank']],
            );
        }
        return $this->returnJson('成功',1001,true,$data);
    }

}