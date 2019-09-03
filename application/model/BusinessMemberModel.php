<?php

namespace app\model;

class BusinessMemberModel extends BaseModel
{
    protected $name = 'business_member';


    public function memberList($data,$limit=10,$business_id=0){

        $member=$this->alias('a')
            ->leftJoin('member m','a.member_id=m.id')
            ->leftJoin('member_bank b','b.member_id=m.id')
            ->where('a.business_id',$business_id)->where('m.is_authentication',1)->where('a.is_delete',0)
        ;
        if(isset($data['name'])){
            $member = $member->where('m.name' ,$data['js_number'] );
        }
        if(isset($data['mobile'])){
            $member = $member->where('m.mobile' ,$data['mobile'] );
        }
       return $list= $member->field('m.name,m.mobile,m.cart_id,b.number,b.address,b.bank_name,b.sub_branch,m.id,a.id as aid')->paginate($limit, false, ['query' => request()->param()]);

    }

    public function deleteMember($id){
        self::where(['id'=>$id])->update(['is_delete'=>1]);
    }

}