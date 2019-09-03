<?php

namespace app\model;

use app\exceptions\ApiException;
use think\Db;

class BusinessModel extends BaseModel
{
    protected $name = 'business';

    public function getBusinessList($data,$limit=10){
        $business=$this->alias('b')
            ->leftJoin('member u','u.id=b.member_id')
            ->leftJoin('service_charge s','s.business_id=b.id')->where('b.is_delete',0)
            ;

        if(isset($data['type'])){
            if(isset($data['keyworld'])){
                if($data['type']==1){
                    $business= $business->whereLike('b.name', '%'.$data['keyworld'].'%');
                }
                if($data['type']==2){
                    $business = $business->where('b.legal_person' ,$data['keyworld'] );
                }
                if($data['type']==3){
                    $business = $business->where('b.contacts_mobile' ,$data['keyworld'] );
                }
                if($data['type']==4){
                    $business = $business->where('b.card_number' ,$data['keyworld'] );
                }
            }
        }
        $business = $business->where('b.status' ,'<>',1);
        return $list= $business->field('b.name,b.contacts,b.contacts_mobile,b.legal_person,b.bank_name,b.card_number,b.invoice_type,b.status,b.id,s.rate,b.province,b.city,b.industry,b.number')->order('b.status desc ,b.id desc')->paginate($limit, false, ['query' => request()->param()]);

    }

    public function detail($id){

        $business=$this->alias('b')
            ->leftJoin('service_charge s','s.business_id=b.id')->where('b.is_delete',0)->where('b.id',$id);
        ;
         $list= $business->field('b.name,b.abbreviation,
        b.taxpayer,b.license,b.address,b.contacts,
        b.contacts_mobile,b.business_mobile,
        b.legal_person,b.legal_person_id,b.id_img_frontal,b.id_img_back,b.bank_name,
        b.subbranch_name,b.bank_address,b.open_permit,b.status,b.invoice_type,b.card_number,s.id as sid,s.rate,b.province,b.industry_code,b.city,b.city_id,b.industry,b.rejection')->find();
        return $list;

    }

    public function updateBusiness($data,$id){
        $result=self::where(['taxpayer'=>$data['taxpayer']])->where('id','<>',$id)->find();
        if($result){
            error('纳税人识别号不能重复',10000);
        }
        self::where(['id'=>$id])->update($data);
    }


    public function getMemberNumber(){
        $num = self::where(['status'=>2])->count();
        return $num+1+10000;
    }


}