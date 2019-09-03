<?php

namespace app\model;

class BusinessAddressModel extends BaseModel
{
    protected $name = 'business_address';


    public function createAddress($data){

        $address=self::where(['business_id'=>$data['business_id']])->find();
        if($address){
            self::where(['business_id'=>$data['business_id']])->update($data);
        }else{
            self::create($data);
        }

    }

    public function addressInfo($id){
        return self::where(['business_id'=>$id])->find();
    }
}