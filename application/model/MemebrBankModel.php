<?php

namespace app\model;

use think\Db;

class MemebrBankModel extends BaseModel
{
    protected $name = 'member_bank';

    public function create_bank($data){
       //晚点放事物
       $bank= Self::where(['member_id'=>$data['member_id']])->find();
       if($bank){
           Self::where(['member_id'=>$data['member_id']])->update($data);
       }else{
           self::create($data);
       }
    }



}