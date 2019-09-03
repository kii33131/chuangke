<?php

namespace app\admin\controller;

use app\model\SubjectModel;
use think\Db;

class Subject extends Base
{
    public function index(SubjectModel $SubjectModel)
    {
        $this->SubjectList = $SubjectModel->getSubjectList($this->limit);
        return $this->fetch();
    }

    public function delete(SubjectModel $SubjectModel)
    {
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('不存在数据');
        }
        if ($SubjectModel->update([
            'id' => $id,
            'is_delete' => 1
        ])) {
            $this->success('删除成功', url('Subject/index'));
        }
        $this->error('删除失败');
    }

    public function create(SubjectModel $SubjectModel)
    {
        if (\think\facade\Request::isPost()){
           $data= \think\facade\Request::post();
           $ch = Db::table('tbl_subject')->where(['name'=>$data['name']])->find();
           if(!$ch){
               $data['created_at'] = date('Y-m-d H:i:s');
               $SubjectModel->store($data) ? $this->success('添加成功', url('Subject/index')) : $this->error('添加失败');
           }else{
               $this->error('重复数据');
           }
        }
        return $this->fetch();
    }

}