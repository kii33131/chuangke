<?php


namespace app\api\controller;

use app\api\validate\AchievementValidate;
use app\api\validate\ContentValidate;
use app\api\validate\Msg;
use app\api\validate\UntyingValidate;
use app\api\validate\UserReceive;
use app\api\validate\UserSms;
use app\model\AchievementModel;
use app\model\AttorneyModel;
use app\model\ContentModel;
use app\model\ContractModel;
use app\model\MemebrBankModel;
use app\model\MemebrModel;
use app\model\MemebrTaskModel;
use app\model\UserModel;
use think\facade\Request;

class User extends Base
{

    // 发送短信
    public function sendMsg(){
        $validate = new UserSms();
        $validate->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        //var_dump($data);exit;
        $code=$this->getCode();
        $userModel = new UserModel();
        $userModel->sendMsg($code,$data['type'],$data['mobile']);
    }

    //登录
    public function login(){
        $validate = new \app\api\validate\User();
        $validate->scene('login')->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        $memeberModel = new MemebrModel();
        //处理验证码
        $result=$memeberModel->checkCode($data['code'],$data['type']);
        if($result['code']!=200){
            error($result['msg'],$result['code']);
        }
        //处理登录
        $uuid = $this->create_guid();
        $business_id =     isset($data['business_id']) && $data['business_id']?$data['business_id']:0;
        $userinfo=$this->doLogin($data['mobile'],$data['type'],$uuid,$business_id);
        success($userinfo);

    }
    //添加银行卡
    public function bank(){
        $validate = new \app\api\validate\User();
        $validate->scene('bank')->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        $data['member_id'] = $this->userinfo['userinfo']['id'];
        $data['created_at'] = date('Y-m-d H:i:s');
        $memberBankModel = new MemebrBankModel();
        $memberBankModel->create_bank($data);
        success([]);

    }

    //用户提交审核信息
    public function examine(){
        $validate = new \app\api\validate\User();
        $validate->scene('examine')->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        $memeberModel = new MemebrModel();
        $data['is_authentication'] =3;
        //处理验证码
        $memeberModel->examine($data,$this->userinfo['userinfo']['id']);
        success([]);

    }

    public function receivingTask(){
        $validate = new \app\api\validate\Task();
        $validate->scene('receivingtask')->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        $memberTaskModel = new MemebrTaskModel();
        $result=$memberTaskModel->receiving_task($data['task_id'],$this->userinfo['userinfo']['id']);
        if(isset($result['url'])){
            success(['url'=>$result['url']]);
        }else{
            success([]);
        }
    }

    public function dealingInvitations(){

        $validate = new \app\api\validate\Task();
        $validate->scene('dealing_invitations')->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        $memberTaskModel = new MemebrTaskModel();
        $result=$memberTaskModel->dealingInvitations($data['member_task_id'],$data['type']);
        if(isset($result['url'])){
            success(['url'=>$result['url']]);
        }else{
            success([]);
        }
    }

    public function confirmationReceipts(){
        $validate = new AchievementValidate();
        $validate->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        //$data = input('post.');
        $model = new AchievementModel();
        $model->confirmation_receipts($data['id']);
        success([]);
    }

    public function info(){
        $model = new MemebrModel();
        $data = input('post.');
        $member= $model->member($this->userinfo['userinfo']['id']);
        success($member);
    }



    public function bankDetail(){
        $model = new MemebrModel();
        $bank= $model->bankDetail($this->userinfo['userinfo']['id']);
        success($bank);
    }

    public function saveUsers(){

        $data = input('post.');
        $userdata =[];
        if(isset($data['channelids']) && $data['channelids']){
            $userdata['channelids'] = $data['channelids'];
        }
        if(isset($data['address']) && $data['address']){
            $userdata['address'] = $data['address'];
        }

        if(isset($data['province']) && $data['province']){
            $userdata['province'] = $data['province'];
        }

        if(isset($data['city']) && $data['city']){
            $userdata['city'] = $data['city'];
        }

        if(isset($data['province_id']) && $data['province_id']){
            $userdata['province_id'] = $data['province_id'];
        }
        if(isset($data['city_id']) && $data['city_id']){
            $userdata['city_id'] = $data['city_id'];
        }

        $bankdata =[];
        if(isset($data['bank_name']) && $data['bank_name']){
            $bankdata['bank_name'] = $data['bank_name'];
        }
        if(isset($data['sub_branch']) && $data['sub_branch']){
            $bankdata['sub_branch'] = $data['sub_branch'];
        }
        if(isset($data['number']) && $data['number']){
            $bankdata['number'] = $data['number'];
        }

        if(isset($data['bankaddress']) && $data['bankaddress']){
            $bankdata['address'] = $data['bankaddress'];
        }

        $model = new MemebrModel();
        $model->saveUsers($userdata,$bankdata,$this->userinfo['userinfo']['id']);
        success([]);
    }


    public function editBank(){
        $data = input('post.');
        $bankdata =[];
        if(isset($data['bank_name']) && $data['bank_name']){
            $bankdata['bank_name'] = $data['bank_name'];
        }
        if(isset($data['sub_branch']) && $data['sub_branch']){
            $bankdata['sub_branch'] = $data['sub_branch'];
        }
        if(isset($data['number']) && $data['number']){
            $bankdata['number'] = $data['number'];
        }
        if(isset($data['bankaddress']) && $data['bankaddress']){
            $bankdata['address'] = $data['bankaddress'];
        }
        $memeberModel = new MemebrModel();
        //处理验证码
        if(isset($data['code']) && $data['code'] ){
            $result=$memeberModel->checkCode($data['code'],3);
            if($result['code']!=200){
                error($result['msg'],$result['code']);
            }
        }else{
            error('验证码不能为空',10000);
        }
        $memeberModel->saveUsers([],$bankdata,$this->userinfo['userinfo']['id']);
        success([]);

    }


    public function untying(){
        $validate = new UntyingValidate();
        $validate->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        $memeberModel = new MemebrModel();
        //处理验证码
        $result=$memeberModel->checkCode($data['code'],4);
        if($result['code']!=200){
            error($result['msg'],$result['code']);
        }
        $memeberModel->untying($data,$this->userinfo['userinfo']['id']);
        success([]);

    }


    public function redirects(){
        $data=input('post.');
        $ContractModel = new ContractModel();
        $ContractModel->initcontract($data);
    }


    public function redirecty(){
        $data=input('post.');
        $AttorneyModel = new AttorneyModel();
        $AttorneyModel->initattorney($data);
    }

    public function submitContent(){
        $validate = new ContentValidate();
        $validate->goCheck();
        $data=input('post.');
        $data['img'] = \GuzzleHttp\json_encode($data['img']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['member_id'] = $this->userinfo['userinfo']['id'];
        $model = new ContentModel();
        $model->create($data);
        success([]);
    }


    public function contentList(){
        $data=input('post.');
        $model = new ContentModel();
        if(!isset($data['id']) || !$data['id']){
            error('缺少用户id',10000);
        }
        $list=$model->contentList($data['id'],$data,$this->listRows);
        success($list);

    }

     public function info2(){
        $model = new MemebrModel();
        $data = input('post.');
        if(!isset($data['id']) || !$data['id']){
            error('缺少用户id',10000);
        }
        $member= $model->member($data['id']);
        success($member);
    }
}