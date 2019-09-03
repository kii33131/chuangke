<?php


namespace app\api\controller;


use app\model\MemebrTaskModel;
use think\Db;

class MemberTask extends Base
{
    public function taskList(){
      $data = input('post.');
      $model =  new MemebrTaskModel();
      $list = $model->task_list($this->userinfo['userinfo']['id'],$this->listRows,$data);
      success($list);
    }
    //创客合同列表
    public function contractList(){
        $data = input('post.');
        $model =  new MemebrTaskModel();
        $list = $model->getContractList($this->userinfo['userinfo']['id'],$this->listRows,$data);
        success($list);
    }

    public function businessContractList(){
        $data = input('post.');
        $model =  new MemebrTaskModel();
        $list = $model->getBusinessContractList($this->userinfo['userinfo']['business_id'],$this->listRows,$data);
        success($list);
    }

}