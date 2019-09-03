<?php


namespace app\api\controller;



use app\api\validate\AddressValidate;
use app\model\BusinessAddressModel;
use app\model\DistrictModel;

class District extends Base
{

    public function addressList(){
        $model = new DistrictModel();
        $result=$model->addressList();
        success($result);
    }


    public function address(){
        $validate = new AddressValidate();
        $validate->goCheck();
        $model = new BusinessAddressModel();
        $data =input('post.');
        $data['business_id'] = $this->userinfo['userinfo']['business_id'];
        $model->createAddress($data);
        success([]);
    }


    public function addressInfo(){
        $model = new BusinessAddressModel();
        $data=$model->addressInfo($this->userinfo['userinfo']['business_id']);
        success($data);
    }

}