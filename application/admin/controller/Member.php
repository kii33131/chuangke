<?php

namespace app\admin\controller;


use app\admin\request\TaskRequest;
use app\admin\request\TemplateRequest;
use app\model\AgentModel;
use app\model\MemebrModel;
use app\model\TemplateModel;
use think\Db;

class Member extends Base
{
    public function index(MemebrModel $member)
    {
        $params = $this->request->param();
        $this->checkParams($params);
        $this->memebr  = $memebrs=$member->memberList($this->limit,$params);
        return $this->fetch();
    }

    public function pass(MemebrModel $member){
        $id = $this->request->post('id');
        $number = '';
        $memberdeatail=$member->member($id);
        $birthday = strlen($memberdeatail->cart_id)==15 ? ('19' . substr($memberdeatail->cart_id, 6, 4)) : substr($memberdeatail->cart_id, 6, 4);
        $number =$memberdeatail->channel[0]['code'].$memberdeatail->city_id.$birthday.$member->getMemberNumber();
        $member->updateBy($id, ['is_authentication'=>1,'number'=>$number]);
        $this->success('通过', url('Member/index'));
    }

    public function refuse(MemebrModel $member){
        $id = $this->request->post('id');
        $rejection = $this->request->post('msg');
        $member->updateBy($id, ['is_authentication'=>2,'rejection'=>$rejection]);
        $this->success('拒绝', url('Member/index'));
    }


    public function detail(MemebrModel $member,$id){
        $this->member= $member->findBy($id);
        $bank= Db::table('tbl_member_bank')->where(['member_id'=>$id])->find();
        $this->assign('bank',$bank);
        $member_channel= Db::table('tbl_member_channel')->where(['member_id'=>$id])->find();
        $channel= Db::table('tbl_channel')->where(['id'=>$member_channel['channel_id']])->find();
        $this->assign('channel',$channel);
        return $this->fetch();
    }
}