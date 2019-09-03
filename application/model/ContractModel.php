<?php

namespace app\model;


use app\exceptions\ApiException;
use think\Db;


class ContractModel extends BaseModel
{
    protected $name = 'contract';


    public function created($data){
        return self::insertGetId($data);
    }

    public function getNum(){
        $num=self::where('created_at','>',date('Y-m-d').' 00:00:00')->count();
        $num = $num+1;
        $num= str_pad($num,4,"0",STR_PAD_LEFT);
        return 'HT'.date('Ymd').$num;
    }

    public function edit($id,$data){
        self::where(['id'=>$id])->update($data);
    }


    public function initcontract($data){
        $MemberModel = new MemberModel();
        $result=$MemberModel->checkMemberByAccountId($data['accountId']);
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
        //echo $contract;exit;
    }

    public function getContractList($data,$limit=10){
        $contract=$this->table('tbl_contract')->alias('a')
            ->leftJoin('tbl_member_task b','a.member_task_id=b.task_id')
            ->leftJoin('tbl_task c','b.task_id=c.id')
            ->leftJoin('tbl_business d','b.business_id=d.id');
        if(isset($data['type'])){
            if(isset($data['keyword'])){
                if($data['type']==1){
                    $contract= $contract->where('a.nail_status=1 AND a.second_status=1 AND a.over_status=1')
                        ->where( "a.number|a.business_name|a.name",'like',"%{$data['keyword']}%" );
                }
                if($data['type']==2){
                    $contract= $contract->where('a.nail_status<>1 OR a.second_status<>1 OR a.over_status<>1')
                        ->where( "a.number|a.business_name|a.name",'like',"%{$data['keyword']}%" );
                }
            }
        }
        $list= $contract->field('a.number,a.flowid,a.business_name,a.name,a.nail_status,a.second_status,a.over_status,a.over_contract_address,c.number as task_number,d.name as business_name')->group('a.number')->order('a.created_at desc')->paginate($limit, false, ['query' => request()->param()]);

        return $list;
    }

    public function getCkList($data,$limit=10){
        $contract=$this->table('tbl_attorney')->alias('a')
            ->leftJoin('tbl_member b','a.member_id=b.id')->where('b.type',1);

        if(isset($data['number'])){
            if(isset($data['name'])){
                if(isset($data['cart'])) {
                    $contract = $contract->whereLike('b.number', '%' . $data['number'] . '%')
                        ->whereLike('b.name', '%' . $data['name'] . '%')
                        ->whereLike('b.cart_id', '%' . $data['cart'] . '%');
                }
            }
        }

        return $list= $contract->field('b.name,b.mobile,b.cart_id,b.number,a.created_at,a.flowid')->order('a.created_at desc')->paginate($limit, false, ['query' => request()->param()]);

    }

}