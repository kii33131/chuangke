<?php

namespace app\model;


use app\exceptions\ApiException;
use think\Db;


class AttorneyModel extends BaseModel
{
    protected $name = 'attorney';

    public function created($data){
        return self::insertGetId($data);
    }


    public function edit($id,$data){
        self::where(['id'=>$id])->update($data);
    }

    public function initattorney($data){
        //echo '<pre>';
        //print_r($data);exit;
        $MemberModel = new MemberModel();
        $result=$MemberModel->checkMemberByAccountId($data['accountId']);
        if($result=='over_status'){
            $result ='nail_status';
        }
        $status = 0;
        if($data['signResult']==2){
            $status =1;
        }
        if($data['signResult']==3){
            $status =3;
        }
        if($data['signResult']==4){
            $status =2;
        }
        self::where(['flowid'=>$data['flowId']])->update([$result=>$status]);
        success([]);
    }

}