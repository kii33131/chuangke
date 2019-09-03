<?php

namespace app\admin\controller;



use app\admin\request\BusinessRequest;
use app\model\BusinessMemberModel;
use app\model\BusinessModel;
use think\Db;

class Business extends Base
{
    public function index(BusinessModel $BusinessModel)
    {
        $params = $this->request->param();
        $this->checkParams($params);
        $this->business = $BusinessModel->getBusinessList($params, $this->limit);
        return $this->fetch();
    }



    /**
     * Edit Data
     *
     * @return mixed|string
     */
    public function edit(BusinessModel $BusinessModel)
    {
        $id = $this->request->param('id');
        if (!$id) {
            $this->error('不存在的数据');
        }
        $business= $BusinessModel->detail($id);
        if (!$business) {
            $this->error('不存在的数据');
        }
        $this->assign('business',$business);
        if (\think\facade\Request::isPost()) {
            $data = \think\facade\Request::post();
            db::table('tbl_service_charge')->where('id',$data['id'])->update($data) !== false ? $this->success('修改服务费率成功', url('Business/index')) : $this->error('');
        }
        return $this->fetch();

    }


    public function delete(BusinessModel $BusinessModel)
    {
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('不存在数据');
        }
        $Business = $BusinessModel->findBy($id);

        //Db::table('tbl_member')->where(['id'=>$Business['member_id']])->update(['is_authentication'=>1]);
        if ($BusinessModel->update([
            'id' => $id,
            'is_delete' => 1
        ])) {
            $this->success('通过成功', url('Business/index'));
        }
        $this->error('通过失败');
    }

    /**
     * 审核通过
     *
     * @return void
     */
    public function pass(BusinessModel $BusinessModel)
    {
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('不存在数据');
        }
        $Business = $BusinessModel->findBy($id);
        //taxpayer
        $number =$Business['industry_code'].$Business['city_id'].substr($Business['taxpayer'], 0, 8).$BusinessModel->getMemberNumber();
        Db::table('tbl_member')->where(['id'=>$Business['member_id']])->update(['is_authentication'=>1]);
        if ($BusinessModel->update([
            'id' => $id,
            'status' => 2,
            'number'=>$number
        ])) {
            $this->success('通过成功', url('Business/index'));
        }
        $this->error('通过失败');
    }

    /**
     * 审核拒绝
     *
     * @return void
     */
    public function refuse(BusinessModel $BusinessModel)
    {
        $id = $this->request->post('id');
        $rejection = $this->request->post('msg');

        if (!$id) {
            $this->error('不存在数据');
        }
        $Business = $BusinessModel->findBy($id);
        Db::table('tbl_member')->where(['id'=>$Business['member_id']])->update(['is_authentication'=>2]);
        if ($BusinessModel->update([
            'id' => $id,
            'status' => 3,
            'rejection'=>$rejection
        ])) {
            $this->success('拒绝成功', url('Business/index'));
        }
        $this->error('拒绝失败');
    }

    public function create(BusinessModel $BusinessModel,BusinessRequest $request ){
        $this->error('暂未额开发');
        if ($request->isPost()) {
            $data = $request->post();
            if(!empty($_FILES) ){
                $pictures = [
                    'license',
                    'id_img_frontal',
                    'id_img_back',
                    'open_permit'
                ];
                foreach ($pictures as $val){
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
            }else{
                throw new ApiException([
                    'msg' => '未上传图片',
                    'errorCode' => '10000'
                ]);
            }
            $member=Db::table('tbl_member')->where(['account'=>$data['contacts_mobile']])->find();
            if(!$member){

                $serverdata['rate'] = $data['rate'];
                $serverdata['type'] = $data['type'];
                unset($data['rate']);
                unset($data['type']);
                $data['member_id']=db::table('tbl_member')->insertGetId(['account'=>$data['contacts_mobile']
                    ,'mobile'=>$data['contacts_mobile'],'is_authentication'=>1,'created_at'=>date('Y-m-d H:i:s'),'type'=>2]);
                $data['status']=2;
                $serverdata['business_id']=$BusinessModel->store($data);
                db::table('tbl_service_charge')->insertGetId($serverdata);
                //Db::commit();
                $this->success('添加成功', url('Business/index'));
            }else{
                $this->error('重复添加');
            }
        }
        return $this->fetch();
    }

    public function view(BusinessModel $BusinessModel,$id){
        $this->business = $BusinessModel->findBy($id);
        return $this->fetch();
    }

}