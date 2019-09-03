<?php


namespace app\api\controller;


use app\model\MemebrTaskModel;
use app\model\TaskModel;

class Task extends Base
{
    //企业发布业务
    public function release(){
        $validate = new \app\api\validate\Task();
        $validate->scene('release')->goCheck();
        $data =input('post.');
        $taskModel= new TaskModel();
        if(!$this->userinfo['userinfo']['business_id']){
         error('您还不是企业不能发布任务',40001);
        }
        $taskModel->release($data,$this->userinfo['userinfo']['id'],$this->userinfo['userinfo']['business_id']);
        success([]);
    }
    //企业业务列表1
    public function index(){
        $data =input('post.');
        $taskModel= new TaskModel();
        if(!$this->userinfo['userinfo']['business_id']){
            error('您还不是企业不能发布任务',40001);
        }
        $list=$taskModel->index($this->listRows,$data,$this->userinfo['userinfo']['business_id']);
        success($list);
    }
    //未指定的创客申请列表
    public function memberList(){
        $validate = new \app\api\validate\Task();
        $validate->scene('memberList')->goCheck();
        $data =input('post.');
        $taskModel= new MemebrTaskModel();
        if(!$this->userinfo['userinfo']['business_id']){
            error('您还不是企业',40001);
        }
        $list=$taskModel->member_list($this->listRows,0,$data['id']);
        success($list);

    }

    //
    public function appointMemberList(){
        $validate = new \app\api\validate\Task();
        $validate->scene('memberList')->goCheck();
        $data =input('post.');
        $taskModel= new MemebrTaskModel();
        if(!$this->userinfo['userinfo']['business_id']){
            error('您还不是企业',40001);
        }
        $list=$taskModel->member_list($this->listRows,1,$data['id']);
        success($list);

    }

    public function examine(){
        $validate = new \app\api\validate\Task();
        $validate->scene('examine')->goCheck();
        $data =input('post.');
        $taskModel= new MemebrTaskModel();
        if(!$this->userinfo['userinfo']['business_id']){
            error('您还不是企业',40001);
        }
        //echo '11';exit;
        $reasons_rejection= isset($data['reasons_rejection'])?$data['reasons_rejection']:'';
        $taskModel->examine($data['member_task_id'],$data['type'],$reasons_rejection);
        success([]);

    }

    public function resubmit(){
        $validate = new \app\api\validate\Task();
        $validate->scene('detail')->goCheck();
        $data =input('post.');
        $MemebrTaskModel= new MemebrTaskModel();
        $MemebrTaskModel->resubmit($data['id']);
        success([]);

    }

        public function details(){

        $validate = new \app\api\validate\Task();
        $validate->scene('detail')->goCheck();
        $data =input('post.');
        $taskModel= new TaskModel();
        $task=$taskModel->detail($data);
        success($task);
    }

    public function lowerShelf(){
        $validate = new \app\api\validate\Task();
        $validate->scene('lowerShelf')->goCheck();
        $data =input('post.');
        $taskModel= new TaskModel();
        $taskModel->lowerShelf($data['ids'],$data['type']);
        success([]);
    }

}