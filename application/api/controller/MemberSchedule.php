<?php


namespace app\api\controller;

use app\api\validate\MemberScheduleValidate;
use app\model\MemberScheduleModel;

class MemberSchedule extends Base
{
    public function submitSchedule(){
        $validate = new MemberScheduleValidate();
        $validate->scene('create')->goCheck();
        $data =input('post.');
        $data['member_id'] = $this->userinfo['userinfo']['id'];
        $model = new MemberScheduleModel();
        if(isset($data['basis'])){
            $data['basis'] =\GuzzleHttp\json_encode($data['basis']);
        }
        if(isset($data['enclosure'])){
            $data['enclosure'] =\GuzzleHttp\json_encode($data['enclosure']);
        }
        $model->create_schedule($data);
        success([]);
    }

    public function scheduleList(){
        $validate = new MemberScheduleValidate();
        $validate->scene('list')->goCheck();
        $data =input('post.');
        $model = new MemberScheduleModel();
        $list=$model->scheduleList($data['member_task_id'],$this->listRows);
        $list = $list->toArray();
        if(isset($list['data'])){
            foreach ($list['data'] as $key=>$v){
                $list['data'][$key]['basis'] = \GuzzleHttp\json_decode($v['basis']);
                if($v['enclosure']){
                    $list['data'][$key]['enclosure'] = \GuzzleHttp\json_decode($v['enclosure']);
                }else{
                    $list['data'][$key]['enclosure'] = [];
                }
            }
        }

        success($list);

    }


    public function drop(){
        $validate = new MemberScheduleValidate();
        $validate->scene('schedule_id')->goCheck();
        $data =input('post.');
        $model = new MemberScheduleModel();
        $model->drop($data['schedule_id']);
        success([]);
    }
}