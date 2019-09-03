<?php


namespace app\api\controller;


use app\model\MemebrModel;
use app\model\MemebrTaskModel;
use app\model\TaskModel;

class Member extends Base
{
    public function detail(){
        $validate = new \app\api\validate\Task();
        $validate->scene('detail')->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        $model = new MemebrModel();
        $member=$model->member($data['id']);
        success($member);
    }

}