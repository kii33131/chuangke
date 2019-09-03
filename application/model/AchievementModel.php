<?php

namespace app\model;


use think\Db;

class AchievementModel extends BaseModel
{
    protected $name = 'achievement';

    public function create_achievement($member_task_id,$member_id){
        //先查找进度没有进度不能提交业绩
        $member_schedule=Db::table('tbl_member_schedule')->where(['member_task_id'=>$member_task_id,'is_delete'=>0])->find();
        if(!$member_schedule){
            error('您还没有进度不能提交业绩',50003);
        }
        $achievement=self::where(['member_task_id'=>$member_task_id])->find();
        if($achievement['type']==1){
            error('已经审核通过不需要在提交了',50004);
        }
        if($achievement){

            self::where(['member_task_id'=>$member_task_id])->update(['type'=>0]);
        }else{

            $num=self::where('created_at','>',date('Y-m-d').' 00:00:00')->count();
            $num = $num+1;
            $num= str_pad($num,4,"0",STR_PAD_LEFT);
            $number = 'JS'.date('Ymd').$num;
            $data = ['member_id'=>$member_id,'member_task_id'=>$member_task_id,'number'=>$number,'created_at'=>date('Y-m-d H:i:s')];
            self::create($data);
        }
    }

    public function achievementList($member_id,$limit,$data){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->leftJoin('task t','t.id=m.task_id')
        ;
        if(isset($data['number'])){
            $achievement = $achievement->where('a.number' ,$data['number'] );
        }
        if(isset($data['task_number'])){
            $achievement = $achievement->where('m.number' ,$data['task_number'] );
        }
        if(isset($data['busiss_name'])){
            $achievement= $achievement->whereLike('b.name', '%'.$data['busiss_name'].'%');
        }

        if(isset($data['type'])){
            //0 待结算 1:待收款 2:以收款
            if($data['type']==1){
                $status=0;
            }
            if($data['type']==2){
                $status=1;
            }
            if($data['type']==3){
                $status=2;
            }
            if($data['type']==6){
                $status=6; //待用户确认
            }

            if($data['type']==4){
                $status=3; //待用户确认
            }

            if($data['type']==5){
                $achievement = $achievement->whereIn('a.status' ,'4,9,10' );
            }else{
                $achievement = $achievement->where('a.status' ,$status );
            }
        }else{
            $achievement = $achievement->whereIn('a.status' ,'0,1,2,6,3,4,7,9,10' );
        }
        $achievement = $achievement->where('m.member_id', $member_id);
        $achievement = $achievement->where('a.type', 1);
        $list =  $achievement->field('t.name as name,b.name as business_name,a.id,a.number,
        a.status,a.commission as money,a.tax as taxes,a.tax_type as payment_party,a.service_money as technical_service_money,
        a.service_pay_type as technical_payment_party,a.money as total_money,m.number as task_number,a.examine_at,a.member_pay,
        a.business_pay,a.payment_vouchers,a.pt_money')
        ->order('a.id desc')->paginate($limit, false, ['query' => request()->param()])->each(function ($item){
            $item->payment_vouchers = json_decode($item->payment_vouchers);
        });;
      //  echo $this->getLastSql();exit;
        return $list;
    }


    public function businessInvoiceList($business_id,$limit,$data){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->where('m.business_id',$business_id)
            ->where('a.status',2);
        if(isset($data['pj_number'])){
            $achievement = $achievement->where('a.pj_number' ,$data['pj_number'] );
        }
        if(isset($data['pj_number'])){
            $achievement = $achievement->where('a.pj_number' ,$data['pj_number'] );
        }
        if(isset($data['type'])){
            if($data['type']==1){
                $achievement = $achievement->where('a.invoice' ,'<>','' );
            }else{
                $achievement = $achievement->where('a.invoice' ,'=','' );
            }
        }
        if(isset($data['fp_type'])){
            $achievement = $achievement->where('a.invoice_type' ,$data['invoice_type']);
        }
        return $achievement->field('a.id,a.pj_number,a.invoice_type,r.name,b.taxpayer,a.accounting_subjects,a.pt_money,a.invoice')->order('a.id desc')->paginate($limit, false, ['query' => request()->param()]);
    }


    public function memberInvoiceList($user_id,$limit,$data){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->where('m.member_id',$user_id)
            ->where('a.status',2);
        if(isset($data['pj_number'])){
            $achievement = $achievement->where('a.pj_number' ,$data['pj_number'] );
        }
        if(isset($data['pj_number'])){
            $achievement = $achievement->where('a.pj_number' ,$data['pj_number'] );
        }
        if(isset($data['type'])){
            if($data['type']==1){
                $achievement = $achievement->where('a.invoice' ,'<>','' );
            }else{
                $achievement = $achievement->where('a.invoice' ,'=','' );
            }
        }
        if(isset($data['fp_type'])){
            $achievement = $achievement->where('a.invoice_type' ,$data['invoice_type']);
        }
        return $achievement->field('a.id,a.pj_number,a.invoice_type,b.name,b.taxpayer,a.accounting_subjects,a.pt_money,a.invoice')->order('a.id desc')->paginate($limit, false, ['query' => request()->param()]);
    }


    public function examine_list($business_id,$limit,$data){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->leftJoin('task t','t.id=m.task_id')
            ->leftJoin('member r','m.member_id=r.id')
        ;
        $achievement = $achievement->where('m.business_id' ,$business_id );
        if(isset($data['number'])){
            $achievement = $achievement->where('a.number' ,$data['number'] );
        }
        if(isset($data['task_name'])){
            $achievement= $achievement->whereLike('t.name', '%'.$data['task_name'].'%');
        }
        if(isset($data['member_name'])){
            $achievement= $achievement->whereLike('r.name', '%'.$data['member_name'].'%');
        }
        if(isset($data['type'])){
            if($data['type']==1){
                $status=0;
            }
            if($data['type']==2){
                $status=1;
            }
            if($data['type']==3){
                $status=2;
            }

            if($data['type']==4){
                $status=3;
            }

            if($data['type']==5){
                $status=4;
            }

            $achievement = $achievement->where('a.status' ,$status );
        }
        $data = $achievement->field('a.id,t.id as tid,t.number,t.explain,t.name as task_name ,r.name as member_name,a.created_at,a.type,a.option')->order('a.id desc')->paginate($limit, false, ['query' => request()->param()]);

        //echo
        return $data;

    }

    public function confirmation_receipts($id){
        $num=self::where('created_at','>',date('Y-m-d').' 00:00:00')->count();
        $num = $num+1;
        $num= str_pad($num,4,"0",STR_PAD_LEFT);
        $number = 'PJ'.date('Ymd').$num;
        self::where(['id'=>$id])->update(['status'=>2,'confirm_time'=>date('Y-m-d H:i:s'),'pj_number'=>$number]);
    }


    public function detail($id){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('member r','m.member_id=r.id')->where('a.id',$id)
            ->field('a.id,a.number,a.option,a.created_at,r.name,a.type,r.id as member_id,a.member_task_id,a.payment_vouchers')->find();
        if(!$achievement){
            error('查不到该业绩信息',50005);
        }
        $data['type'] = $achievement['type'];
        $data['option'] = $achievement['option'];
        $data['id'] = $achievement['id'];
        $data['member_id'] = $achievement['member_id'];
        $data['name'] = $achievement['name'];
        $data['created_at'] = $achievement['created_at'];
        $data['number'] = $achievement['number'];
        //$data['payment_vouchers'] = $achievement['payment_vouchers'];
        $data['payment_vouchers'] = json_decode($achievement['payment_vouchers']);
        $schedule=Db::table('tbl_member_schedule')->where(['member_task_id'=>$achievement['member_task_id'],'is_delete'=>0])->select();
        $data['schedule'] =$schedule;
        return $data;
    }

    public function examine($id,$typ,$option='',$confirm_mobile=''){
      self::where(['id'=>$id])->update(['type'=>$typ,'examine_at'=>date('Y-m-d H:i:s'),'option'=>$option,'confirm_mobile'=>$confirm_mobile]);
    }

    public function settlementDetail($id){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('task t','t.id=m.task_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->leftJoin('service_charge s','m.business_id=s.business_id')->where('a.id',$id)
            ->field('
            a.id,
            a.number,
            a.created_at,
            t.id as task_id, 
            r.name,
            t.number as task_number,
            s.rate,
            s.type,
            a.commission,
            a.tax_type,
            a.service_pay_type,
            a.member_pay,
            a.business_pay,
            a.service_money,
            a.tax,
            t.name as task_name,
            a.pt_money,a.invoice_type,a.accounting_subjects,a.accounting_subjects_son,a.pt_money')->find();
        ;
        if($achievement){
            $tax_rate=Db::table('tbl_config')->find();
            $achievement['tax_rate'] = $tax_rate['tax_rate']*0.01;
            $achievement['rate']  = $achievement['rate']*0.01;
            $payment_log=Db::table('tbl_payment_log')->where(['achievement_id'=>$achievement['id']])->find();
            if($payment_log){
                $achievement['voucher'] = \GuzzleHttp\json_decode($payment_log['voucher']);
            }else{
                $achievement['voucher'] =[];
            }
        }
        return $achievement;
    }

    public function settlementSubmission($data){
        $id =$data['id'];
        unset($data['id']);
        self::where(['id'=>$id])->update($data);
    }


    public function managementList($data,$business_id,$limit=10){

        if($data['status']==1){
            $where = '0,3,5,6,7';
           return $this->managementSettlementList($where,$data,$business_id,$limit=10);

        }elseif ($data['status']==2){
            //4 待支付 1平台已支付  9 企业发出支付待平台确认 10 :平台确认企业支付
            $where = '4,1,9,10';
            return $this->managementSettlementPayList($where,$data,$business_id,$limit=10);
        }else{
            error('status 不合法',50005);
        }

    }


    public function managementSettlementList($where,$data,$business_id,$limit=10){

        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->leftJoin('task t','t.id=m.task_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->where('b.id',$business_id)
            ->where('a.type',1)
        ;

        if(isset($data['js_number'])){
            $achievement = $achievement->where('a.number' ,$data['js_number'] );
        }
        if(isset($data['type'])){
            $achievement = $achievement->where('a.status' ,$data['type'] );
        }else{
            $achievement = $achievement->whereIn('a.status',$where);

        }
        if(isset($data['member_name'])){
            $achievement= $achievement->whereLike('r.name', '%'.$data['member_name'].'%');
        }

        $list= $achievement->order('a.id desc')->field('a.id,a.number,t.number as task_number,t.name  as task_name, r.name, a.examine_at, a.status,a.option,a.confirm_mobile')->paginate($limit, false, ['query' => request()->param()]);;

       // echo $this->getLastSql();exit;
       return $list;
    }



    public function managementSettlementPayList($where,$data,$business_id,$limit=10){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->leftJoin('task t','t.id=m.task_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->where('b.id',$business_id)
            ->where('a.type',1)
        ;

        if(isset($data['js_number'])){
            $achievement = $achievement->where('a.number' ,$data['js_number'] );
        }
        if(isset($data['type'])){
            if($data['type']==1){
                $achievement = $achievement->whereIn('a.status','1,9,10');
            }else{
                $achievement = $achievement->where('a.status' ,$data['type'] );

            }
        }else{
            $achievement = $achievement->whereIn('a.status',$where);

        }
        if(isset($data['member_name'])){
            $achievement= $achievement->whereLike('r.name', '%'.$data['member_name'].'%');
        }
        $list= $achievement->order('a.id desc')->field('a.id,a.number,t.number as task_number,t.name  as task_name, r.name, a.commission, a.status,a.tax,a.tax_type,a.service_money,a.service_pay_type,a.money,a.member_pay,a.business_pay,a.ptconfirm_time,a.pt_money')->paginate($limit, false, ['query' => request()->param()]);
        return $list;
    }

    public function dopay($data,$business_id){
        $achievement_id = $data['achievement_id'];
        $achievement=self::where(['id'=>$achievement_id,'status'=>4])->find();
        if(!$achievement){
            error('结算信息错误不能生成支付',50006);
        }
        $result = [
            'business_id'=>$business_id,
            'money'=>$achievement['pt_money'],
            'voucher'=>\GuzzleHttp\json_encode($data['voucher']),
            'created_at'=>date('Y-m-d H:i:s'),
            'achievement_id'=>$achievement_id,
            'bank_id'=>$data['bank_id']
        ];
        Db::table('tbl_payment_log')->insert($result);
        //9代表客企业已发出付款请求待平台付款 1
        self::where(['id'=>$achievement_id,'status'=>4])->update(['status'=>9]);
        //self::where(['id'=>$achievement_id,'status'=>4])->update(['status'=>1]);
    }

    public function bank(){
      return  Db::table('tbl_bank')->find();
    }


    public function AdminAchievementList($params,$limit){
        $achievement=$this->alias('a')
        ->leftJoin('member_task m','a.member_task_id=m.id')
        ->leftJoin('task t','t.id=m.task_id')
        ->leftJoin('member r','m.member_id=r.id')
        ->leftJoin('business b','b.id=m.business_id')
        ->whereIn('a.status','3,4,5')
        ;
        if(isset($params['number'])){
            $achievement = $achievement->where('a.number' ,$params['number'] );
        }
        if(isset($params['task_number'])){
            $achievement = $achievement->where('t.number' ,$params['task_number'] );
        }
        if(isset($params['name'])){
            $achievement = $achievement->where('m.name' ,$params['name'] );
        }
        if(isset($params['business_name'])){
            $achievement = $achievement->where('b.name' ,$params['business_name'] );
        }

        $list= $achievement->order('a.status asc,a.id desc')->field('a.id,a.number,t.number as task_number,t.name  as task_name, r.name, a.commission, a.status,a.tax,a.tax_type,a.service_money,a.service_pay_type,a.money,a.member_pay,a.business_pay,b.name as business_name,a.pt_money')->paginate($limit, false, ['query' => request()->param()]);
        return $list;
    }


    public function billList($params,$limit){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->where('a.status',2);
        if(isset($params['pj_number'])){
            $achievement = $achievement->where('a.pj_number' ,$params['pj_number'] );
        }
        if(isset($params['pj_number'])){
            $achievement = $achievement->where('a.pj_number' ,$params['pj_number'] );
        }
        if(isset($data['type'])){
            if($params['type']==1){
                $achievement = $achievement->where('a.invoice' ,'<>','' );
            }else{
                $achievement = $achievement->where('a.invoice' ,'=','' );
            }
        }
        if(isset($params['invoice_type'])){
            $achievement = $achievement->where('a.invoice_type' ,$params['invoice_type']);
        }
        return $achievement->field('a.id,a.pj_number,a.invoice_type,b.name as bname,r.name,b.taxpayer,a.accounting_subjects,a.pt_money,a.invoice')->order('a.id desc')->paginate($limit, false, ['query' => request()->param()]);
    }

    public function AdminAchievementListPay($params,$limit){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('task t','t.id=m.task_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->whereIn('a.status','9,10,1,2')
        ;
        if(isset($params['number'])){
            $achievement = $achievement->where('a.number' ,$params['number'] );
        }
        if(isset($params['status'])){
            $achievement = $achievement->where('a.status' ,$params['status'] );
        }
        if(isset($params['name'])){
            $achievement = $achievement->where('b.name' ,$params['name'] );
        }

        $list= $achievement->order('a.id desc')->field('a.id,a.number,b.name ,a.commission, a.tax_type, a.service_pay_type, a.service_money,a.pt_money,a.status,a.tax')->paginate($limit, false, ['query' => request()->param()]);
        return $list;
    }


    public function shukuang($id){

        self::where(['id'=>$id])->update(['status'=>10]);
    }


    public function AdminAchievementListincome($params,$limit){
        $achievement=$this->alias('a')
            ->leftJoin('member_task m','a.member_task_id=m.id')
            ->leftJoin('task t','t.id=m.task_id')
            ->leftJoin('member r','m.member_id=r.id')
            ->leftJoin('business b','b.id=m.business_id')
            ->whereIn('a.status','10,1,2,9')
        ;
        if(isset($params['number'])){
            $achievement = $achievement->where('a.number' ,$params['number'] );
        }
        if(isset($params['status'])){
            $achievement = $achievement->where('a.status' ,$params['status'] );
        }
        if(isset($params['name'])){
            $achievement= $achievement->whereLike('b.name', '%'.$params['name'].'%');

        }
        $list= $achievement->order('a.id desc')->field('r.name as member_name, a.id,a.number,b.name as business_name ,a.commission, a.tax_type, a.service_pay_type, a.service_money,a.member_pay,a.pt_money,a.status,a.tax,t.name as task_name,t.number as task_number,a.payment_vouchers,r.id as rid')->paginate($limit, false, ['query' => request()->param()]);
        return $list;
    }

    public function userConfirm($id){

        self::where(['id'=>$id])->update(['status'=>3]);
    }

    public function userRefuseAchievement($id,$option){
        self::where(['id'=>$id])->update(['status'=>7,'option'=>$option]);
    }


    public function deatil($id){
        $achievement=$this->alias('a')
        ->leftJoin('member_task m','a.member_task_id=m.id')
        ->leftJoin('task t','t.id=m.task_id')
        ->leftJoin('member r','m.member_id=r.id')
        ->leftJoin('business b','b.id=m.business_id')
        ->leftJoin('payment_log p','p.achievement_id=a.id')
         ->leftJoin('contract c','c.member_task_id=m.id')
         ->where('a.id',$id)
        ->field('a.pj_number,t.number as tnumber,a.number as anumber,
        a.payment_vouchers,r.name ,a.pt_money,a.invoice_type,a.accounting_subjects,a.accounting_subjects_son,b.name as bname,b.taxpayer,c.number as c_number,c.flowid,
        b.business_mobile,b.address,b.bank_address,b.card_number,a.invoice,p.voucher,a.money,a.service_money,a.member_pay,a.business_pay,a.id,t.id as tid,a.created_at
        ')->find();
        $documentSignflows=documentSignflows($achievement['flowid']);

       // echo '<pre>';
        //print_r($documentSignflows);exit;
        if(isset($documentSignflows['data']['docs'][0]['fileUrl'])){
            $achievement['fileUrl']=$documentSignflows['data']['docs'][0]['fileUrl'];
        }else{
            $achievement['fileUrl'] ='';
        }

        $achievement['payment_vouchers'] = json_decode($achievement['payment_vouchers']);
        $achievement['invoice'] = json_decode($achievement['invoice']);
        $achievement['voucher'] = json_decode($achievement['voucher']);
        return $achievement;
    }



    public function findMemberScheduleByid($id){
        $memberschedule=$this->alias('a')
        ->leftJoin('member_task m','a.member_task_id=m.id')
        ->leftJoin('member_schedule s','m.id=s.member_task_id')
        ->leftJoin('member u','u.id=s.member_id')
        ->where('a.id',$id)->where('s.is_delete',0)
        ->field('s.start_time,s.end_time,s.completion_degree,s.basis,s.created_at')->select()->each(function ($item){
                $item->basis = json_decode($item->basis);
            });;
        return $memberschedule;
    }
}