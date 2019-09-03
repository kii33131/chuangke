<?php

namespace app\model;

use think\Db;

class MemebrTaskModel extends BaseModel
{
    protected $name = 'member_task';


    public function member_list($limit,$type=0,$id){
        $task=$this->alias('t')
            ->join('member m','t.member_id=m.id')
            ->where('t.type','=',$type)->where('t.task_id','=',$id );
        if($type==1){
            return $task->field('t.id as member_task_id,m.id,m.name as name ,t.response_at,t.response_type as status,t.reasons_rejection')->order('t.id desc')->paginate($limit, false, ['query' => request()->param()]);

        }else{
            return $task->field('t.id as member_task_id,m.id,m.name as name ,t.created_at,t.status,t.examine_at,t.reasons_rejection')->order('t.id desc')->paginate($limit, false, ['query' => request()->param()]);

        }

    }

    public function examine($id,$type,$reasons_rejection=''){
        $membertask=self::find($id);
        if(!$membertask){
            error("找不到该任务流",10000);
        }
        if($type==1){
            $this->hetongcommon($type,$membertask,$id,2,$reasons_rejection);
        }else{
            self::where(['id'=>$id])->update(['status'=>$type,'examine_at'=>date('Y-m-d H:i:s'),'reasons_rejection'=>$reasons_rejection]);
        }

    }

    public function getEsign(){
       return Db::table('tbl_esign')->where(['is_default'=>1])->find();
    }

    public function hetongcommon($type,$membertask,$id,$state=1,$reasons_rejection=''){
        Db::startTrans();
        try {
            $Esign=$this->getEsign();
            $contractresult=Db::table('tbl_contract')->lock(true)->where(['member_task_id'=>$id])->select();
            if($contractresult){
                return '';
            }
            if($state==1){
                self::where(['id'=>$id])->update(['response_type'=>$type,'response_at'=>date('Y-m-d H:i:s')]);
            }else{
                self::where(['id'=>$id])->update(['status'=>$type,'examine_at'=>date('Y-m-d H:i:s'),'reasons_rejection'=>$reasons_rejection]);
            }
            $config=ConfigModel::getConfigs();
            //生成合同流程
            $memberModel = new MemberModel();
            //处理创客
            $resultMember=$memberModel->getMemberDetail($membertask['member_id']);
            $e_data = [];
            if($resultMember['accountid']){
                $e_data[] = ['accountid'=>$resultMember['accountid'],'orgid'=>'','sealid'=>'','x'=>config('e_ck_x'),'y'=>config('e_ck_y')];
            }else{
                //创建e签宝帐号
                $return=createByThirdPartyUserId($resultMember['id'],$resultMember['name'],$resultMember['mobile'],$resultMember['cart_id']);
                if(isset($return['data']['accountId']) && $return['data']['accountId']){
                    Db::table('tbl_member')->where(['id'=>$membertask['member_id']])->update(['accountid'=>$return['data']['accountId']]);
                    $e_data[] = ['accountid'=>$return['data']['accountId'],'orgid'=>'','sealid'=>'','x'=>config('e_ck_x'),'y'=>config('e_ck_y')];
                }else{
                    error("创客添加e签宝失败",10000);
                }
            }
            //处理企业
            $result=$memberModel->getMemberBybusinessId($membertask['business_id']);
            if($result['accountid']){
                $e_data[] = ['accountid'=>$result['accountid'],'orgid'=>$result['orgid'],'sealid'=>$result['sealid'],'x'=>config('e_ql_x'),'y'=>config('e_ql_y')];
            }else{
                //创建e签宝帐号
                $return= createByThirdPartyUserId($result['id'],$result['bname'],$result['mobile'],$result['legal_person_id']);//legal_person_id
                if(isset($return['data']['accountId']) && $return['data']['accountId']){
                    $businessreturn= orgCreateByThirdPartyUserId($return['data']['accountId'],$result['id'].'b',$result['bname'],$result['taxpayer']);
                    if(isset($businessreturn['data']['orgId']) && $businessreturn['data']['orgId']){
                        $officialtemplate=officialtemplate($businessreturn['data']['orgId']);
                        Db::table('tbl_member')->where(['id'=>$result['id']])->update(['accountid'=>$return['data']['accountId'],'orgid'=>$businessreturn['data']['orgId'],'sealid'=>$officialtemplate['data']['sealId']]);
                        $e_data[] = ['accountid'=>$return['data']['accountId'],'orgid'=>$businessreturn['data']['orgId'],'sealid'=>$officialtemplate['data']['sealId'],'x'=>config('e_ql_x'),'y'=>config('e_ql_y')];
                    }else{
                        Db::rollback();
                        error("创客添加e签宝失败",10000);
                    }
                }else{
                    Db::rollback();
                    error("创客添加e签宝失败1",10000);
                }
            }

            $signflows=signflows($config['file_name']);
            $flowId =  isset($signflows['data']['flowId'])?$signflows['data']['flowId']:'';
            if(!$flowId){
                Db::rollback();
                error("签署流程创建失败",10000);
            }
            $createByTemplate=createByTemplate($config['template_id'],$config['file_name'],$config['template_sign_zonea'],
                $config['template_sign_zoneb'],$config['template_sign_zonec'],1,$config['template_sign_wz_a'],
                $config['template_sign_wz_b'],$config['template_sign_wz_c'],$Esign['org_name'],$resultMember['name'],$result['bname']);
            $fileId =  isset($createByTemplate['data']['fileId'])?$createByTemplate['data']['fileId']:'';
            if(!$fileId){
                Db::rollback();
                error("通过模板创建文件失败",10000);
            }
            documents($flowId,$fileId);
            $e_data[] = ['accountid'=>$Esign['account_id'],'orgid'=>$Esign['org_id'],'sealid'=>$Esign['seal_id'],'x'=>config('e_pt_x'),'y'=>config('e_pt_y')];
            foreach ($e_data as $k=>$v){
                 handSign($flowId,$fileId,$v['accountid'],$v['x'],$v['y'],$v['orgid'],$v['sealid']);
            }

            start($flowId);
            $contractmodel = new ContractModel();
            $num = $contractmodel->getNum();
            $contract_id = $contractmodel->created(['member_task_id'=>$id,'name'=>$resultMember['name'],'flowid'=>$flowId,'fileid'=>$fileId,'number'=>$num,'created_at'=>date('Y-m-d H:i:s')]);
            foreach ($e_data as $k=>$v){
                if($k==0){
                    $executeUrl=executeUrl($flowId,$v['accountid'],'');
                    $contract['second_contract_address'] =$executeUrl['data']['url'];
                }elseif ($k==1){
                    $executeUrl=executeUrl($flowId,$v['accountid'],$v['orgid']);

                    $contract['nail_contract_address'] =$executeUrl['data']['url'];
                }elseif ($k==2){
                    $executeUrl=executeUrl($flowId,$v['accountid'],$v['orgid']);
                    $contract['over_contract_address'] =$executeUrl['data']['url'];
                }
                $contractmodel->edit($contract_id,$contract);
            }
            //合同生成完成
            Db::commit();
        } catch (ApiException $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }catch(\Exception $e){
            Db::rollback();
            throw $e;
        }

    }

    //领取任务
    public function receiving_task($task_id,$member_id){
        Db::startTrans();
        try {
            $result=self::lock(true)->where([
                'member_id'=>$member_id,
                'task_id'=>$task_id
            ])->find();
            if($result){
                Db::rollback();
                error('您已经领取过啦',50001);
            }else{
                $Esign=$this->getEsign();
                $task=Db::table('tbl_task')->where(['id'=>$task_id,'is_delete'=>0])->find();
                if($task){
                    Self::insert([
                        'member_id'=>$member_id,
                        'task_id'=>$task_id,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'business_id'=>$task['business_id'],
                        'number'=>$task['number']
                    ]);
                    //创建委托书开始
                    $attorney=Db::table('tbl_attorney')->where(['member_id'=>$member_id])->find();
                    if(!$attorney){
                        $config=ConfigModel::getConfigs2();
                        $memberModel = new MemberModel();
                        //处理创客
                        $resultMember=$memberModel->getMemberDetail($member_id);
                        $e_data = [];
                        if($resultMember['accountid']){
                            $e_data[] = ['accountid'=>$resultMember['accountid'],'orgid'=>'','sealid'=>'','x'=>config('e_wtck_x'),'y'=>config('e_wtck_y')];
                        }else{
                            //创建e签宝帐号
                            $return=createByThirdPartyUserId($resultMember['id'],$resultMember['name'],$resultMember['mobile'],$resultMember['cart_id']);
                            if(isset($return['data']['accountId']) && $return['data']['accountId']){
                                Db::table('tbl_member')->where(['id'=>$member_id])->update(['accountid'=>$return['data']['accountId']]);
                                $e_data[] = ['accountid'=>$return['data']['accountId'],'orgid'=>'','sealid'=>'','x'=>config('e_wtck_x'),'y'=>config('e_wtck_y')];
                            }else{
                                Db::rollback();
                                error("创客添加e签宝失败",10000);
                            }
                        }
                        $signflows=signflows($config['file_name'],2);
                        $flowId =  isset($signflows['data']['flowId'])?$signflows['data']['flowId']:'';
                        if(!$flowId){
                            Db::rollback();
                            error("签署流程创建失败",10000);
                        }
                        $createByTemplate=createByTemplate($config['template_id'],$config['file_name'],$config['template_sign_zonea'],$config['template_sign_zoneb'],$config['template_sign_zonec'],2);
                        $fileId =  isset($createByTemplate['data']['fileId'])?$createByTemplate['data']['fileId']:'';
                        if(!$fileId){
                            Db::rollback();
                            error("通过模板创建文件失败",10000);
                        }
                        documents($flowId,$fileId);
                        $e_data[] = ['accountid'=>$Esign['account_id'],'orgid'=>$Esign['org_id'],'sealid'=>$Esign['seal_id'],'x'=>config('e_wtql_x'),'y'=>config('e_wtql_y')];
                        foreach ($e_data as $k=>$v){
                            if($k==0){
                                handSign($flowId,$fileId,$v['accountid'],$v['x'],$v['y'],$v['orgid'],$v['sealid'],2);
                            }else{
                               platformSign($flowId,$fileId,$v['x'],$v['y'],$v['sealid'],2);
                            }                        }
                        start($flowId);
                        $attModel = new AttorneyModel();
                        $att_id = $attModel->created(['member_id'=>$member_id,'flowid'=>$flowId,'fileid'=>$fileId,'created_at'=>date('Y-m-d H:i:s')]);
                        foreach ($e_data as $k=>$v){
                            if($k==0){
                                $executeUrl=executeUrl($flowId,$v['accountid'],'');

                                $contract['second_contract_address'] =$executeUrl['data']['url'];
                            }elseif ($k==1){
                                //平台自动签署委托书
                                $contract['nail_status'] =1;
                            }
                            $attModel->edit($att_id,$contract);
                        }

                        Db::commit();
                        return  ['code'=>200,'url'=>$contract['second_contract_address']];

                    }else{
                        if($attorney['second_status']!=1){
                            Db::commit();
                            return  ['code'=>200,'url'=>$attorney['second_contract_address']];
                        }else{
                            Db::commit();
                        }
                    }

                }else{
                    Db::rollback();
                    error('无效的业务',70001);
                }
            }

        } catch (ApiException $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }catch(\Exception $e){
            Db::rollback();
            throw $e;
        }

    }


    public function task_list($member_id,$limit,$data=[]){
        $membertask=$this->alias('m')
        ->leftJoin('task t','m.task_id=t.id')
        ->leftJoin('business s','s.id=m.business_id')
        ->leftJoin('achievement a1','m.id=a1.member_task_id')->where('t.type',1)
        ;
        if(isset($data['type'])){
            if(in_array($data['type'],[4,5])){
                $membertask = $membertask->join('achievement a','m.id=a.member_task_id');
            }
        }
        if((isset($data['type']) && !in_array($data['type'],[4,5]))||!isset($data['type'])){
            $membertask->where('m.member_id','=',$member_id);
            $membertask->where('a1.type <> 1   or a1.type is null');
        }
        if(isset($data['number'])){
            $membertask = $membertask->where('m.number','=',$data['number']);
        }
        if(isset($data['name'])){
            $membertask =$membertask->whereLike('t.name', '%'.$data['name'].'%');
        }
        if(isset($data['type'])){
          if(in_array($data['type'],[1,2,3])){
            if($data['type']==1){
                $type =0;//待审核
            }
            if($data['type']==2){
                  $type =1;//通过
            }
            if($data['type']==3){
                  $type =2;//驳回
            }
            $membertask = $membertask->where('m.status',$type);
            $membertask = $membertask->where('m.type',0);
          }
          if(in_array($data['type'],[6,7,8])){

                if($data['type']==6){
                    $response_type =0;//待审核
                }
                if($data['type']==7){
                    $response_type =1;//待审核
                }
                if($data['type']==8){
                    $response_type =2;//驳回
                }
                $membertask = $membertask->where('m.response_type','=',$response_type);
                $membertask = $membertask->where('m.type','=',1);
          }
          if(in_array($data['type'],[4,5])){
                if($data['type']==4){
                    $type =0;//提交业绩待审核
                }
                if($data['type']==5){
                    $type =2;//提交业绩已驳回
                }
                $membertask = $membertask->where('a.type','=',$type);
          }
        }
        $list= $membertask
            ->field(
                'm.number as task_number,
                t.name as task_nam,
                t.explain,
                s.name as business_name,
                t.created_at,
                m.type,
                m.status,
                m.response_type,
                m.id,
                t.id as task_id,
                m.reasons_rejection
                ')
            ->order('t.id desc')
            ->paginate($limit, false, ['query' => request()->param()])->each(function ($item){

                $achievement=Db::table('tbl_achievement')->where(['member_task_id'=>$item->id])->find();
                if($achievement){
                    $item->status=5; //业绩已提交
                    if($achievement['type']==2){

                        $item->status=3;//业绩被驳回

                    }else{
                        $item->status=4;//业绩待审核
                    }
                    $item->option = $achievement['option'];
                }
            });

        return $list;
    }

    public function dealingInvitations($id,$type){
        $membertask=self::find($id);
        if(!$membertask){
            error("找不到该任务流",10000);
        }
        if($type==1){
                //同意成功签署委托书
                //创建委托书开始
            $Esign=$this->getEsign();
            $attorney=Db::table('tbl_attorney')->where(['member_id'=>$membertask['member_id']])->find();
                if(!$attorney){
                    $config=ConfigModel::getConfigs2();
                    $memberModel = new MemberModel();
                    //处理创客
                    $resultMember=$memberModel->getMemberDetail($membertask['member_id']);
                    $e_data = [];
                    if($resultMember['accountid']){
                        $e_data[] = ['accountid'=>$resultMember['accountid'],'orgid'=>'','sealid'=>'','x'=>config('e_wtck_x'),'y'=>config('e_wtck_y')];
                    }else{
                        //创建e签宝帐号
                        $return=createByThirdPartyUserId($resultMember['id'],$resultMember['name'],$resultMember['mobile'],$resultMember['cart_id']);
                        if(isset($return['data']['accountId']) && $return['data']['accountId']){
                            Db::table('tbl_member')->where(['id'=>$membertask['member_id']])->update(['accountid'=>$return['data']['accountId']]);
                            $e_data[] = ['accountid'=>$return['data']['accountId'],'orgid'=>'','sealid'=>'','x'=>config('e_wtck_x'),'y'=>config('e_wtck_y')];
                        }else{
                            error("创客添加e签宝失败",10000);
                        }
                    }
                    $signflows=signflows($config['file_name'],2);
                    $flowId =  isset($signflows['data']['flowId'])?$signflows['data']['flowId']:'';
                    if(!$flowId){
                        error("签署流程创建失败",10000);
                    }
                    $createByTemplate=createByTemplate($config['template_id'],$config['file_name'],$config['template_sign_zonea'],$config['template_sign_zoneb'],$config['template_sign_zonec'],2);
                    $fileId =  isset($createByTemplate['data']['fileId'])?$createByTemplate['data']['fileId']:'';
                    if(!$fileId){
                        error("通过模板创建文件失败",10000);
                    }
                    documents($flowId,$fileId);
                    $e_data[] = ['accountid'=>$Esign['account_id'],'orgid'=>$Esign['org_id'],'sealid'=>$Esign['seal_id'],'x'=>config('e_wtql_x'),'y'=>config('e_wtql_y')];
                    foreach ($e_data as $k=>$v){
                        if($k==0){
                            handSign($flowId,$fileId,$v['accountid'],$v['x'],$v['y'],$v['orgid'],$v['sealid'],2);
                        }else{
                            platformSign($flowId,$fileId,$v['x'],$v['y'],$v['sealid'],2);
                        }
                    }
                    start($flowId);
                    $attModel = new AttorneyModel();
                    $att_id = $attModel->created(['member_id'=>$membertask['member_id'],'flowid'=>$flowId,'fileid'=>$fileId,'created_at'=>date('Y-m-d H:i:s')]);
                    foreach ($e_data as $k=>$v){
                        if($k==0){
                            $executeUrl=executeUrl($flowId,$v['accountid'],'');
                            $contract['second_contract_address'] =$executeUrl['data']['url'];
                        }elseif ($k==1){
                            //平台自动签署委托书
                            $contract['nail_status'] =1;

                        }
                        $attModel->edit($att_id,$contract);
                    }
                    return  ['code'=>200,'url'=>$contract['second_contract_address']];

                }else{
                    if($attorney['second_status']!=1){
                        return  ['code'=>200,'url'=>$attorney['second_contract_address']];
                    }
                }

            $this->hetongcommon($type,$membertask,$id);
        }else{
            self::where(['id'=>$id])->update(['response_type'=>$type,'response_at'=>date('Y-m-d H:i:s')]);
        }
    }

    public function resubmit($id){
        self::where(['id'=>$id,'status'=>2])->update(['status'=>0]);
    }


    public function getContractList($member_id,$limit,$data=[]){
        $contract=$this->alias('m')
        ->leftJoin('task t','m.task_id=t.id')
        ->leftJoin('business s','s.id=m.business_id')
        ->leftJoin('contract c','c.member_task_id=m.id');
        $contract = $contract->where('m.member_id',$member_id);
        if(isset($data['type']) && isset($data['name'])){
            if($data['type']==1){
                //合同编号
                $contract = $contract->where('c.number',$data['name']);
            }
            if($data['type']==2){
                //业务单号
                $contract = $contract->where('t.number',$data['name']);
            }
            if($data['type']==3){
                //企业名称
                $contract =$contract->whereLike('b.name', '%'.$data['name'].'%');
            }
        }
        if(isset($data['status'])){
            $contract = $contract->where('c.second_status',$data['status']);
        }
        $contract = $contract->where('c.number is not null');
        return $contract->order('c.id desc')->field('c.number,t.number as tnumber,c.second_contract_address,
        s.name as bname,c.nail_status,c.second_status,c.over_status,c.flowid,t.name as tname')
        ->paginate($limit, false, ['query' => request()->param()])->each(function ($item){
                $documentSignflows=documentSignflows($item->flowid);
                if(isset($documentSignflows['data']['docs'][0]['fileUrl'])){
                    $item->fileUrl=$documentSignflows['data']['docs'][0]['fileUrl'];
                }else{
                    $item->fileUrl ='';
                }
            });

    }


    public function getBusinessContractList($business_id,$limit,$data=[]){
        $contract=$this->alias('m')
            ->leftJoin('task t','m.task_id=t.id')
            ->leftJoin('business s','s.id=m.business_id')
            ->leftJoin('contract c','c.member_task_id=m.id');
        $contract = $contract->where('s.id',$business_id);
        if(isset($data['type']) && isset($data['name'])){
            if($data['type']==1){
                //合同编号
                $contract = $contract->where('c.number',$data['name']);
            }
            if($data['type']==2){
                //业务单号
                $contract = $contract->where('t.number',$data['name']);
            }
            if($data['type']==3){
                //名称
                $contract =$contract->whereLike('c.name', '%'.$data['name'].'%');
            }
        }
        if(isset($data['status'])){
            $contract = $contract->where('c.nail_status',$data['status']);
        }
        $contract = $contract->where('c.number is not null');
        return $contract->order('c.id desc')->field('c.number,t.number as tnumber,
        c.nail_contract_address,c.name as cname,c.nail_status,c.second_status,c.over_status,c.flowid,t.name as tname')
        ->paginate($limit, false, ['query' => request()->param()])->each(function ($item){
                $documentSignflows=documentSignflows($item->flowid);
                if(isset($documentSignflows['data']['docs'][0]['fileUrl'])){
                    $item->fileUrl=$documentSignflows['data']['docs'][0]['fileUrl'];
                }else{
                    $item->fileUrl ='';
                }
            });
    }
}