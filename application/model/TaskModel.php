<?php

namespace app\model;

use think\Db;

class TaskModel extends BaseModel
{
    protected $name = 'task';

    public function release($data,$member_id,$business_id){

        Db::startTrans();
        try {

            $num=self::where('created_at','>',date('Y-m-d').' 00:00:00')->count();
            $num = $num+1;
            $num= str_pad($num,4,"0",STR_PAD_LEFT);
            $result_ids =[];
            if(isset($data['result_ids'])){
                $result_ids=$data['result_ids'];
                if(!$result_ids){
                    error('result_ids 不能为空',10000);
                }
            }
            unset($data['result_ids']);
            $data['created_at'] =date('Y-m-d H:i:s');
            $data['member_id']=$member_id;
            $data['business_id']=$business_id;
            $data['number'] = 'YW'.date('Ymd').$num;
            if(isset($data['enclosure']) && $data['enclosure']){
                $data['enclosure'] = \GuzzleHttp\json_encode($data['enclosure']);
            }
            if(isset($data['id'])){
                $updatedata  =['explain'=>$data['explain'],'name'=>$data['name'],'type'=>0];
                if(isset($data['enclosure']) && $data['enclosure']){
                    $updatedata['enclosure'] = \GuzzleHttp\json_encode($data['enclosure']);
                }
                self::where(['id'=>$data['id']])->update($updatedata);

            }else{
                $result=self::create($data);
                if($data['push_type']==2){
                    // 指定创客处理逻辑
                    if(!$result_ids){
                        error('result_ids 不能为空',10000);
                    }
                    if($result_ids){
                        foreach ($result_ids as $v){
                            Db::table('tbl_member_task')->insert([
                                'member_id'=>$v,
                                'task_id'=>$result->id,
                                'business_id'=>$business_id,
                                'type'=>1,
                                'number'=>$data['number'],
                                'created_at'=>date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
                if($data['push_type']==3){
                    // 指定创客群处理
                    if(!$result_ids){
                        error('result_ids 不能为空',10000);
                    }
                    if($result_ids){
                        foreach ($result_ids as $v){
                            Db::table('tbl_task_channel')->insert([
                                'channel_id'=>$v,
                                'task_id'=>$result->id,
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }
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

    public function index($limit = self::LIMIT,$params,$business_id){
        $task=$this->alias('t')
            ->join('member m','t.member_id=m.id')
            ->where('t.is_delete','=',0)->where('t.business_id','=',$business_id);
        if (isset($params['number'])) {
            $task = $task->where('t.number', $params['number']);
        }
        if (isset($params['name'])) {
            $task = $task->whereLike('t.name', '%'.$params['name'].'%');
        }

        if(isset($params['type']) && $params['type']){
            $task = $task->where('t.type', $params['type']);
        }

        if(isset($params['lower_shelf']) ){
            $task = $task->where('t.lower_shelf', $params['lower_shelf']);
        }
        if(isset($params['start_time']) && isset($params['end_time'])){
            $task = $task->where('t.created_at' , '>',$params['start_time']);
            $task = $task->where('t.created_at' , '<',$params['end_time']);
        }
        return $task->field('t.id,t.number,t.name,t.explain,m.mobile as phone,t.push_type,t.created_at,t.type,t.lower_shelf,t.reasons')->order('t.id desc')->paginate($limit, false, ['query' => request()->param()]);
    }

    public function lowerShelf($ids,$type){
        $lower_shelf = $type==1?1:0;
        if(!empty($ids)){

            foreach ($ids as $v){

                self::where(['id'=>$v])->update(['lower_shelf'=>$lower_shelf]);
            }
        }

    }

    public function admin_index($limit = self::LIMIT,$params){
        $task=$this->alias('t')
            ->join('business b','t.business_id=b.id')
            ->where('t.is_delete','=',0);
        if(isset($params['status'])){
            if(isset($params['name'])){
                if($params['status']==1){
                    $task = $task->whereLike('t.name', '%'.$params['name'].'%');
                }
                if($params['status']==2){
                    $task = $task->where('t.number' ,$params['name'] );
                }
                if($params['status']==3){
                    $task = $task->whereLike('b.name', '%'.$params['name'].'%');
                }
            }
        }

        if(isset($params['type']) && $params['type']){
            $task = $task->where('t.type', $params['type']);
        }
        return $task->field('t.id,t.number,t.name,b.name as business_name,t.created_at,t.type')->order('t.id desc')->paginate($limit, false, ['query' => request()->param()]);
    }



    public function task_list($limit = self::LIMIT,$params){

        $task=$this->alias('t')
            ->leftJoin('task_channel c','t.id=c.task_id')
            ->leftJoin('business b','t.business_id=b.id')->where('t.type',1)->where('t.lower_shelf',0)->where('t.push_type','<>',2)
        ;
        if(isset($params['name'])){
            $type =isset($params['type'])&& $params['type']?$params['type']:1;
            if($type==1){
                $task= $task->whereLike('t.name', '%'.$params['name'].'%');
            }else{
                $task= $task->whereLike('b.name', '%'.$params['name'].'%');
            }
        }
        if(isset($params['channel_id'])){
            $task = $task->where('c.channel_id', $params['channel_id']);
        }

        return  $task->Distinct(true)->field('t.id')->field('t.id,b.name as business_name ,t.name,t.created_at')->order('t.id desc')->paginate($limit, false, ['query' => request()->param()]);
    }

    public function detail($data){
        $task=self::get($data['id']);
        if($task){
            $member_id=0;
            if(isset($data['token'])){
                $member=MemebrModel::getUserinfoByToken($data['token']);
                if($member){
                    $member_id= $member['member_id'];
                }
            }
            if($member_id){
                $member_task=Db::table('tbl_member_task')->where(['member_id'=>$member_id,'task_id'=>$task->id])->find();
                if($member_task){
                    $task['is_rec'] =1;
                }else{
                    $task['is_rec'] =0;
                }
            }else{
                $task['is_rec'] =0;
            }
            if($task['enclosure']){
                $task['enclosure'] = \GuzzleHttp\json_decode( $task['enclosure']);

            }else{
                $task['enclosure'] = [];
            }
            $task['memberlist'] =Db::table('tbl_member_task')->where(['type'=>1,'task_id'=>$task->id])->field('member_id')->select();
            $task['channel']= Db::table('tbl_task_channel')->where(['task_id'=>$task->id])->field('channel_id')->select();
            return $task;
        }else{
            return [];
        }
    }


    public function admindetail($tid){
        $detail=$this->detail(['id'=>$tid]);
        return $detail;
    }



}