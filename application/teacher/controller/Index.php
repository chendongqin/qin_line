<?php
namespace app\teacher\controller;

use base\Teacherbase;
use think\Session;
class index extends Teacherbase
{

    public function index(){
        $teacher = Session::get('teacher');
        $teacher = isset($teacher[0])?$teacher[0]:$teacher;
        echo 'hello~ ,'.$teacher['teacher_name'];
    }


}