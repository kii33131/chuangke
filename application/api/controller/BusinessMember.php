<?php


namespace app\api\controller;


use app\model\BusinessMemberModel;

class BusinessMember extends Base
{

    public function memberList(){
        $data =input('post.');
        $model = new BusinessMemberModel();
        $list=$model->memberList($data,$this->listRows,$this->userinfo['userinfo']['business_id']);
        success($list);
    }

    public function delete(){
        $data =input('post.');
        $model = new BusinessMemberModel();
        $model->deleteMember($data['id']);
        success([]);
    }

}