<?php

namespace app\admin\controller;

use app\admin\request\StoreRequest;
use app\model\IndustryCategoryModel;
use app\model\StoreModel;

class StoreManage extends Base
{
    public function index(StoreModel $storeModel)
    {
        $params = $this->request->param();
        $this->checkParams($params);
        $params['state'] = 2;
        $this->stores = $storeModel->getList($params, $this->limit);
        return $this->fetch();
    }

    /**
     * Edit Data
     *
     * @return mixed|string
     */
    public function edit(StoreModel $storeModel, StoreRequest $request)
    {
        $id = $this->request->param('id');
        if (!$id) {
            $this->error('不存在的数据');
        }
        $store = $storeModel->findBy($id);
        if (!$store) {
            $this->error('不存在的数据');
        }
        if ($request->isPost()) {
            $data = $request->post();
            //营业时间处理
            $business_hours = explode(' - ',$data['business_hours']);
            $data['start_hours'] = $business_hours[0];
            $data['end_hours'] = $business_hours[1];
            //图片上传
            unset($data['file']);
            $pictures = [
                'logo',
                'id_card_positive',
                'id_card_back',
                'business_license'
            ];
            foreach ($pictures as $val){
                unset($data[$val]);
                if(!empty($_FILES[$val]['name'])){
                    $file = request()->file($val);
                    $info = $file->move(config('upload_file'));
                    if($info){
                        $data[$val] = $info->getSaveName();
                    }else{
                        $this->error($file->getError());
                    }
                }
            }
            $old_pictures = [
                'logo' => $store->logo,
                'id_card_positive' => $store->id_card_positive,
                'id_card_back' => $store->id_card_back,
                'business_license' => $store->business_license
            ];
            //店铺图上传
            $old_exhibition = $store->exhibition;
            if(!is_array($old_exhibition)){
                $old_exhibition = [];
            }
            if(empty($data['exhibition'])){
                $data['exhibition'] = [];
            }
            $exhibition = $data['exhibition'];
            if($storeModel->updateBy($data['id'], $data) !== false){
                //删除旧图片
                foreach ($pictures as $val){
                    if(!empty($data[$val]) && !empty($old_pictures[$val]) && $old_pictures[$val] != $data[$val] ){
                        @unlink(config('upload_file') . $old_pictures[$val]);
                    }
                }
                foreach ($old_exhibition as $val){
                    if(!in_array($val,$exhibition)){
                        @unlink(config('upload_file') . $val);
                    }
                }
                $this->success('编辑成功', url('StoreManage/index'));
            }else{
                $this->error('');
            }
        }
        $store->exhibition = $store->exhibition;
        $this->assign('parentIndustryCategory',IndustryCategoryModel::getParentCategory());
        $store->industry_category_pid = IndustryCategoryModel::where('id',$store['industry_category_id'])->value('pid');
        $this->assign('store',$store);
        return $this->fetch();
    }

    /**
     * Delete Data
     *
     * @return void
     */
    public function delete(StoreModel $storeModel)
    {
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('不存在数据');
        }
        $store = $storeModel->findBy($id);
        if (!$store) {
            $this->error('不存在的数据');
        }
        if($storeModel->updateBy($id, [
                'is_delete' => 1
            ]) !== false) {
            $this->success('删除成功', url('StoreManage/index'));
        }
        $this->error('删除失败');
    }

    public function getIndustryCategoryByPid(){
        return IndustryCategoryModel::field('id,name')->where('pid',input('pid'))->select();
    }

    public function upload(){
        $file = request()->file('file');
        $info = $file->move(config('upload_file'));
        if($info){
            return json([
                'errorCode' => 0,
                'data' => [
                    'url' => $info->getSaveName()
                ]
            ]);
        }else{
            return json([
                'errorCode' => 10001,
                'msg' => '上传图片失败'
            ]);
        }
    }
}