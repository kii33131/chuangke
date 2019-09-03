<?php

namespace app\admin\controller;



use app\admin\request\TaskRequest;
use app\model\AchievementModel;
use app\model\TaskModel;

class Task extends Base
{

    public function index(TaskModel $taskModel,TaskRequest $request){
        $params = $this->request->param();
        $this->checkParams($params);
        $this->tasks  =$taskModel->admin_index($this->limit,$params);
        return $this->fetch();
    }


    public function pass(TaskModel $taskModel){
        $id = $this->request->post('id');
        $taskModel->updateBy($id, ['type'=>1]);
        $this->success('通过', url('Task/index'));
    }

    public function refuse(TaskModel $taskModell){
        $id = $this->request->post('id');
        $reasons = $this->request->post('msg');
        $taskModell->updateBy($id, ['type'=>2,'reasons'=>$reasons]);
        $this->success('拒绝', url('Task/index'));
    }

    public function detail(TaskModel $taskModel,$id,$tid,AchievementModel $achievementModel){
        $data=$taskModel->admindetail($tid);
        $this->assign('dateil',$data);
        $MemberSchedule=$achievementModel->findMemberScheduleByid($id);
        $this->assign('memberschedule',$MemberSchedule);
        $achievement =$achievementModel->deatil($id);
        $this->assign('achievement',$achievement);
        return $this->fetch();

    }

}