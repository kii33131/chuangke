<?php

namespace app\model;


use app\exceptions\ApiException;
use think\Db;

class MemberModel extends BaseModel
{
   protected $name = 'member';

   public function getMemberDetail($id){
     return self::get($id);
   }

   public function getMemberBybusinessId($business_id){
       $member=$this->alias('m');
       $member->leftJoin('business b','m.id=b.member_id')->where('b.id',$business_id);
       return $member->field('m.accountid,m.orgid,m.sealid,m.id,m.mobile,m.name,b.name as bname,b.taxpayer,b.legal_person_id')->find();
   }

   public function checkMemberByAccountId($accountId){
       //$Config=ConfigModel::getConfigsByAccount($accountId);
       $Esign= EsignModel::getEsignByAccount($accountId);
       if($Esign){
           return 'over_status';
       }else{
           $member=self::where(['accountid'=>$accountId])->find();
           if($member){
               if($member['type']==1){
                   return 'second_status';
               }
               if($member['type']==2){
                   return 'nail_status';
               }
           }else{
               return 'over_status';
           }

       }

   }
}