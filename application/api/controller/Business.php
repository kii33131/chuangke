<?php


namespace app\api\controller;


use app\api\validate\EditBusiness;
use app\model\BusinessModel;

class Business extends Base
{
   public function detail(){
       $BusinessModel= new BusinessModel();
       $Business=$BusinessModel->detail($this->userinfo['userinfo']['business_id']);
       success($Business);
   }

   public function updateBusiness(){
       $validate = new \app\api\validate\Business();
       $validate->goCheck();
       $data = $validate->getDataByRule(input('post.'));
       $BusinessModel= new BusinessModel();
       $data['status'] =4;
       $BusinessModel->updateBusiness($data,$this->userinfo['userinfo']['business_id']);
       success([]);
   }


   public function editBusiness(){

       $validate = new EditBusiness();
       $validate->goCheck();
       $data = $validate->getDataByRule(input('post.'));
       $BusinessModel= new BusinessModel();
       $BusinessModel->updateBusiness($data,$this->userinfo['userinfo']['business_id']);
       success([]);
   }
}