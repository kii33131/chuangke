<?php

namespace app\model;

use think\Db;

class MemberScheduleModel extends BaseModel
{
    protected $name = 'member_schedule';


    public function create_schedule($data){
       $member_task= Db::table('tbl_member_task')->get($data['member_task_id']);
       if(!$member_task){
           error('没有找到您领取的任务信息','50002');
       }
       $contract=Db::table('tbl_contract')->where(['member_task_id'=>$data['member_task_id']])->find();
       if($contract){
           if($contract['second_status']!=1){
               error('请先签署合同','10000');
           }
           if($contract['nail_status']!=1){
               error('请企业签署合同','10000');
           }

           if($contract['over_status']!=1){
               error('请平台签署','10000');
           }
       }else{
           error('找不到合同信息','10000');
       }
       $data['created_at'] = date('Y-m-d H:i:s');
       Self::create($data);
    }

    public function scheduleList($member_task_id,$limit){

      return  self::where(['member_task_id'=>$member_task_id,'is_delete'=>0])->order('id desc')->paginate($limit, false, ['query' => request()->param()]);
    }


    public function drop($id){
        self::where(['id'=>$id])->update(['is_delete'=>1]);
    }
}