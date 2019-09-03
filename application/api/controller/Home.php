<?php


namespace app\api\controller;


use app\api\validate\Position;
use app\api\validate\Region;
use app\model\BannerModel;
use app\model\IndustryCategoryModel;
use app\model\MemebrModel;
use app\model\MemebrTaskModel;
use app\model\StoreModel;
use app\model\TaskModel;
use think\Db;


class Home extends Base
{
    //首页
    public function index(){

        $data =input('post.');
        $taskModel= new TaskModel();
        $member_id=0;
        if(isset($data['token'])){
            $member=MemebrModel::getUserinfoByToken($data['token']);
            if($member){
                $member_id= $member['member_id'];
            }
        }
        $list=$taskModel->task_list($this->listRows,$data);
        $list = $list->toArray();
        if($list['data']){
            foreach ($list['data'] as $k=>$v){
                $res=Db::table('tbl_member_task')->where(['member_id'=>$member_id,'task_id'=>$v['id']])->find();
                if($res){
                    $list['data'][$k]['is_rec']=1;
                }else{
                    $list['data'][$k]['is_rec']=0;
                }
            }
        }
        success($list);
    }


}