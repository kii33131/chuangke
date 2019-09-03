<?php

namespace app\admin\controller;



use app\admin\validates\ChannelValidate;
use app\model\ChannelModel;
use think\Db;
use think\Request;

class Channel extends Base
{
    public function index(ChannelModel $ChannelModel)
    {
        $this->ChannelList = $ChannelModel->getChannelList($this->limit);
        return $this->fetch();
    }

    public function delete(ChannelModel $ChannelModel)
    {
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('不存在数据');
        }
        if ($ChannelModel->update([
            'id' => $id,
            'is_delete' => 1
        ])) {
            $this->success('删除成功', url('Channel/index'));
        }
        $this->error('删除失败');
    }

    public function create(ChannelModel $ChannelModel)
    {
        if (\think\facade\Request::isPost()){
           $data= \think\facade\Request::post();
           $ch = Db::table('tbl_channel')->where(['name'=>$data['name']])->find();
           if(!$ch){
               $data['created_at'] = date('Y-m-d H:i:s');
               $ChannelModel->store($data) ? $this->success('添加成功', url('Channel/index')) : $this->error('添加失败');
           }else{
               $this->error('重复数据');
           }
        }
        return $this->fetch();
    }

}