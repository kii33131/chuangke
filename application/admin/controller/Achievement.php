<?php

namespace app\admin\controller;
use app\admin\request\BannerRequest;
use app\model\AchievementModel;
use app\model\BannerModel;

class Achievement extends Base
{
    public function index(AchievementModel $achievementModel)
    {
        $params = $this->request->param();
        $this->checkParams($params);
        $this->achievement = $achievement = $achievementModel->AdminAchievementList($params, $this->limit);
        return $this->fetch();
    }

    public function pass(AchievementModel $achievementModel){
        $id = $this->request->post('id');
        $achievementModel->updateBy($id, ['status'=>4,'ptconfirm_time'=>date('Y-m-d H:i:s')]);
        $this->success('通过', url('Achievement/index'));
    }

    public function shukuang(AchievementModel $achievementModel,$id){
        //$id = $this->request->post('id');
        $achievementModel->updateBy($id, ['status'=>10]);
        $this->success('确认成功', url('Achievement/pay'));
    }

    public function refuse(AchievementModel $achievementModel){
        $id = $this->request->post('id');
        $msg = $this->request->post('msg');
        $achievementModel->updateBy($id, ['status'=>5,'option'=>$msg,'ptconfirm_time'=>date('Y-m-d H:i:s')]);
        $this->success('拒绝', url('Achievement/index'));
    }

    public function pay(AchievementModel $achievementModel){
        $params = $this->request->param();
        $this->checkParams($params);
        $this->achievement = $achievement = $achievementModel->AdminAchievementListPay($params, $this->limit);
        return $this->fetch();
    }

    public function income(AchievementModel $achievementModel){
        $params = $this->request->param();
        $this->checkParams($params);
        $this->achievement = $achievement = $achievementModel->AdminAchievementListincome($params, $this->limit);
        return $this->fetch();
    }


    public function detail(AchievementModel $achievementModel,$id){
        $settlementDetail=$achievementModel->settlementDetail($id);
        $this->assign('settlementDetail',$settlementDetail);
        return $this->fetch();

    }

    public function voucher(AchievementModel $achievementModel,$id){
        $achievement=$achievementModel->detail($id);
        $this->assign('achievement',$achievement);
        return $this->fetch();

    }

    public function paymentvouchers(AchievementModel $achievementModel){
        if (\think\facade\Request::isPost()) {
            $data = \think\facade\Request::post();
            unset($data['file']);
            $data['payment_vouchers'] = json_encode($data['imgs']);
            $data['status'] =1;
            $achievementModel->updateBy($data['id'], $data) !== false ? $this->success('上传成功', url('Achievement/income')) : $this->error('');
        }
        $id = $this->request->param('id');
        if (!$id) {
            $this->error('不存在的数据');
        }
        $this->achievement = $achievementModel->findBy($id);
        return $this->fetch();
    }


    public function invoice(AchievementModel $achievementModel){
        if (\think\facade\Request::isPost()) {
            $data = \think\facade\Request::post();
            //invoice
            unset($data['file']);
            $data['invoice'] = json_encode($data['imgs']);
            $achievementModel->updateBy($data['id'], $data) !== false ? $this->success('上传成功', url('Achievement/bill')) : $this->error('');
        }
        $id = $this->request->param('id');
        if (!$id) {
            $this->error('不存在的数据');
        }
        $this->achievement = $achievementModel->findBy($id);
        return $this->fetch();
    }


    public function bill(AchievementModel $achievementModel){
        $params = $this->request->param();
        $this->checkParams($params);
        $this->achievement = $achievement = $achievementModel->billList($params, $this->limit);
        return $this->fetch();
    }


    public function view(AchievementModel $achievementModel,$id){
        //$id = $this->request->get('id');
        //var_dump($id);
        $this->achievement = $achievement = $achievementModel->deatil($id);
        return $this->fetch();
    }


    public function invoicedetail(AchievementModel $achievementModel,$id){
        $achievement=$achievementModel->deatil($id);
        $this->assign('achievement',$achievement);
        return $this->fetch();

    }


    public function payvoucher(AchievementModel $achievementModel,$id){
        $achievement=$achievementModel->deatil($id);
        $this->assign('voucher',$achievement['voucher']);
        return $this->fetch();
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