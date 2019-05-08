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

class user extends Adminbase
{


    public function index()
    {
        $name = $this->getParam('name', '', 'string');
        $mobile = $this->getParam('mobile', '', 'string');
        $pageLimit = $this->getParam('pageLimit', 15, 'int');
        $page = $this->getParam('page', 1, 'int');
        $where = ['isdel' => 0];
        if ($name) {
            $where['user_name|user_no'] = array('like', $name . '%');
        }
        if ($mobile) {
            $where['mobile'] = $mobile;
        }
        $admins = Db::name('user')
            ->where($where)
            ->order('id', 'desc')
            ->paginate($pageLimit, false, array('page' => $page))
            ->toArray();
        $this->assign('pager', $admins);
        $this->assign('pageLimit', $pageLimit);
        $this->assign('page', $page);
        return $this->fetch();
    }

    //添加学生
    public function add()
    {
        $mobile = $this->getParam('mobile', '');
        $password = $this->getParam('password', '');
        $user_name = $this->getParam('userName');
        $birthday = $this->getParam('birthday');
        $balance = $this->getParam('balance', 0);
        $balance = is_numeric($balance) ? $balance : 0;
        $sex = $this->getParam('sex');
        if (!Verify::isMobile($mobile)) {
            return $this->returnJson('用户手机格式错误');
        }
        if (Db::name('user')->where(['mobile' => $mobile, 'isdel' => 0])->find()) {
            return $this->returnJson($mobile . '用户已存在');
        }
        $pwd = $this->createPwd($password);
        $today = date('Ymd');
        $count = Db::name('user')->where('create_at >= ' . $today . '000000 and create_at <= ' . $today . '235959')->count('id');
        $sn = sprintf("%04d", $count + 1);
        $data = array(
            'mobile'    => $mobile,
            'user_name' => $user_name,
            'birthday'  => $birthday,
            'sex'       => $sex,
            'balance'   => $balance,
            'password'  => $pwd,
            'create_at' => date('YmdHis'),
            'user_no'   => date('YmdH') . $sn . mt_rand(10, 99),
        );
        $res = Db::name('user')->insert($data);
        if($res){
            return $this->returnJson('成功',1001,true);
        }
        return $this->returnJson('错误');
    }

    //删除
    public function del()
    {
        $id = $this->getParam('id');
        if (!$user = Db::name('user')->where(array('id' => $id, 'isdel' => 0))->find()) {
            return $this->returnJson('用户不存在');
        }
        $user['isdel'] = 1;
        $user['update_at'] = date('YmdHis');
        $res = Db::name('user')->update($user);
        if (!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功', 1001, true);
    }

    //信息修改
    public function update()
    {
        $id = $this->getParam('id');
        if (!$user = Db::name('user')->where(array('id' => $id, 'isdel' => 0))->find()) {
            return $this->returnJson('用户不存在');
        }
        // 数据库没有这个字段
        // $user['sex'] = $this->getParam('sex',$user['sex'],'int');
        $user['user_name'] = $this->getParam('userName');
        $user['mobile'] = $this->getParam('mobile');
        if (!Verify::isMobile($user['mobile'])) {
            return $this->returnJson('手机号格式不正确');
        }
        $user['birthday'] = date('Y-m-d', strtotime($this->getParam('birthday', date('Y-m-d'))));
        $user['update_at'] = date('YmdHis');
        $res = Db::name('user')->update($user);
        if (!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功', 1001, true);
    }

    //密码重置
    public function unsetpwd()
    {
        $id = $this->getParam('id');
        if (!$user = Db::name('user')->where(array('id' => $id, 'isdel' => 0))->find()) {
            return $this->returnJson('教师不存在');
        }
        $pwd = $this->getParam('password');
        if (strlen($pwd) < 6) {
            return $this->returnJson('密码长度大于6位');
        }
        $user['password'] = $this->createPwd($pwd);
        $user['update_at'] = date('YmdHis');
        $res = Db::name('user')->update($user);
        if (!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功', 1001, true);
    }

    //充值
    public function recharge()
    {
        $userId = $this->getParam('id');
        if (!$user = Db::name('user')->where(array('id' => $userId, 'isdel' => 0))->find())
            return $this->returnJson('用户不存在');
        $amount = $this->getParam('amount');
        if (!is_numeric($amount))
            return $this->returnJson('金额格式不能正确');
        $user['balance'] += $amount;
        $res = Db::name('user')->update($user);
        if (!$res)
            return $this->returnJson('失败');
        return $this->returnJson('成功', 1001, true);

    }

    //用户报名上课
    public function attend()
    {
        $userId = $this->getParam('userId');
        if (!$user = Db::name('user')->where(array('id' => $userId, 'isdel' => 0))->find())
            return $this->returnJson('用户不存在');
        $courseId = $this->getParam('courseId');
        if (!$course = Db::name('course')->where(array('id' => $courseId))->find())
            return $this->returnJson('课程不存在');
        if ($courseIn = Db::name('course_in')->where(array('user_id' => $userId, 'course_id' => $courseId))->find()) {
            return $this->returnJson('该用户已报名该课程');
        }
        $today = date('Y-m-d');
        if ($course['begin_date'] < $today)
            return $this->returnJson('课程已开始');
        if ($user['balance'] < $course['fee']) {
            return $this->returnJson('用户余额不足');
        }
        if ($course['people'] + 1 > $course['max_people']) {
            return $this->returnJson('超过人数限制');
        }
        $add = array(
            'user_id'   => $user['id'],
            'course_id' => $course['id'],
            'create_at' => date('YmdHis')
        );
        Db::startTrans();
        $res = Db::name('course_in')->insert($add);
        if (!$res) {
            Db::rollback();
            return $this->returnJson('失败');
        }
        $user['balance'] -= $course['fee'];
        $user['update_at'] = date('YmdHis');
        $res = Db::name('user')->update($user);
        if (!$res) {
            Db::rollback();
            return $this->returnJson('失败');
        }
        $course['people'] += 1;
        $res = Db::name('course')->update($course);
        if (!$res) {
            Db::rollback();
            return $this->returnJson('失败');
        }
        Db::commit();
        return $this->returnJson('成功', 1001, true);
    }

}