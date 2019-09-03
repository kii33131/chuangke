<?php


namespace app\api\controller;



use app\api\validate\AchievementExamineValidate;
use app\api\validate\AchievementRefuseValidate;
use app\api\validate\AchievementValidate;
use app\api\validate\BillDetailsValidate;
use app\api\validate\DopayValidate;
use app\api\validate\ManagementListValidate;
use app\api\validate\MemberScheduleValidate;
use app\api\validate\SettlementSubmissionValidate;
use app\model\AchievementModel;

class Achievement extends Base
{
    //11
    public function submitted(){
        $validate = new MemberScheduleValidate();
        $validate->scene('list')->goCheck();
        $data =input('post.');
        $model = new AchievementModel();
        $model->create_achievement($data['member_task_id'],$this->userinfo['userinfo']['id']);
        success([]);

    }

    public function achievementList(){
        $data =input('post.');
        $model = new AchievementModel();
        $list=$model->achievementList($this->userinfo['userinfo']['id'],$this->listRows,$data);
        success($list);
    }

    public function achievementList2(){
        $model = new AchievementModel();
        $data = input('post.');
        if(!isset($data['id']) || !$data['id']){
            error('缺少用户id',10000);
        }
        $list=$model->achievementList($data['id'],$this->listRows,$data);
        success($list);
    }

    public function examineList(){
        $data =input('post.');
        $model = new AchievementModel();
        $list=$model->examine_list($this->userinfo['userinfo']['business_id'],$this->listRows,$data);
        success($list);

    }

    public function detail(){
        $validate = new AchievementValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $result=$model->detail($data['id']);
        success($result);
    }


    public function examine(){
        $validate = new AchievementExamineValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $option= isset($data['option'])?$data['option']:'';
        $model->examine($data['id'],$data['type'],$option,$this->userinfo['userinfo']['mobile']);
        success([]);
    }


    public function settlementDetail(){

        $validate = new AchievementValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $result=$model->settlementDetail($data['id']);
        success($result);
    }

    public function settlementSubmission(){
        $validate = new SettlementSubmissionValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        if($data['service_pay_type']==3 && (!isset($data['member_pay']) || !isset($data['business_pay']))){
            error('请填写平坦支付金额',10000);
        }
        $result=$model->settlementDetail($data['id']);
        $service_money = $data['money']*$result['rate'];
        if($data['service_pay_type']==3){
            if(($data['member_pay']+$data['business_pay'])!=$service_money){
                 //echo $data['member_pay']+$data['business_pay'].'_'.$service_money;exit;

                error('平摊金额只必须要与服务费相等',10000);
            }
        }
        $data['service_money'] = $service_money;
        $data['tax']  = $data['money']*$result['tax_rate'];
        $data['status']=6;//3; //提交待用户确认
        $data['commission'] =$data['money'];

        $ptmoney =$data['money']+ $data['tax']+$service_money;
        if($data['service_pay_type']==3){
            $data['money'] = $data['commission']-$data['member_pay'];
            $ptmoney = $ptmoney-$data['member_pay'];
            //$data['pt_money'] =$data['commission'];
        }elseif ($data['service_pay_type']==1){

            $data['money'] = $data['commission'];
            $data['business_pay'] =$service_money;
            $data['member_pay'] = 0;

        }elseif ($data['service_pay_type']==2){

            $data['money'] = $data['commission']-$service_money;
            $ptmoney = $ptmoney-$service_money;
            $data['business_pay'] =0;
            $data['member_pay'] = $service_money;

        }
        if($data['tax_type']==2){
            $data['money'] = $data['money'] - $data['tax'];
            $ptmoney = $ptmoney-$data['tax'];
        }
        $data['pt_money'] =$ptmoney;

        $model->settlementSubmission($data);
        success([]);

    }

    public function managementList(){
        $validate = new ManagementListValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $list=$model->managementList($data,$this->userinfo['userinfo']['business_id'],$this->listRows);
        success($list);
    }

    public function payPage(){

        $validate = new AchievementValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $result=$model->settlementDetail($data['id']);
        success($result);
    }


    public function dopay(){
        $validate = new DopayValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $model->dopay($data,$this->userinfo['userinfo']['business_id']);
        success([]);
    }


    public function businessInvoiceList(){
        $model = new AchievementModel();
        $data =input('post.');
        $list=$model->businessInvoiceList($this->userinfo['userinfo']['business_id'],$this->listRows,$data);
        success($list);
    }

    public function memberInvoiceList(){
        $model = new AchievementModel();
        $data =input('post.');
        $list=$model->memberInvoiceList($this->userinfo['userinfo']['id'],$this->listRows,$data);
        success($list);
    }

    public function bank(){
        $model = new AchievementModel();
        $bank =$model->bank();
        success($bank);
    }

    //用户确认结算单
    public function userConfirmAchievement(){
        $validate = new AchievementValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $model->userConfirm($data['id']);
        success([]);
    }


    public function userRefuseAchievement(){
        $validate = new AchievementRefuseValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $model->userRefuseAchievement($data['id'],$data['option']);
        success([]);
    }

    public function billDetails(){
        $validate = new BillDetailsValidate();
        $validate->goCheck();
        $model = new AchievementModel();
        $data =input('post.');
        $bill=$model->deatil($data['id']);
        success($bill);
    }
}