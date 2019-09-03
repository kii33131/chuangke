<?php

namespace app\admin\controller;

use app\admin\request\ContractRequest;
use app\model\ContractModel;
use think\Db;

class Contract extends Base
{

    public function index(ContractModel $ContractModel)
    {
        $params = $this->request->param();
        $this->checkParams($params);
        $this->contract = $ContractModel->getContractList($params, $this->limit);
        return $this->fetch();
    }
    //创客委托书列表
    public function ck(ContractModel $ContractModel)
    {
        $params = $this->request->param();
        $this->checkParams($params);
        $this->contract = $ContractModel->getCkList($params, $this->limit);
        return $this->fetch();
    }

    //上传合同模板
    public function createContract(ContractModel $ContractModel,ContractRequest $request)
    {
        if($request->isPost()){
            if(!empty($_FILES) ){
                //移动到public/assets/uploads/目录下
                $info = move_uploaded_file($_FILES["file"]["tmp_name"],config('upload_file').$_FILES["file"]["name"]);
                if($info){
                    $filePath = config('upload_file').$_FILES["file"]["name"];
                    $fileName = $_FILES["file"]["name"];
                    //e签宝模板管理-通过上传方式创建模板
                    $reqTemplate=createByUploadUrl($filePath,$fileName);
                    if($reqTemplate['code']==0){
                        $fileName=explode(".",$fileName);
                        Db::table('tbl_config')->where(['id'=>1])->update(['template_id'=>$reqTemplate['data']['templateId'],'file_name'=>$fileName[0]]);
                        //e签宝文件流上传方法
                        $reqUpload=uploadUrl($filePath,$reqTemplate['data']['uploadUrl']);
                        if($reqUpload['errCode']==0){
                            sleep(10);
                            //e签宝添加输入项组件
                            $reqComponents=components($reqTemplate['data']['templateId']);
                            if($reqComponents['code']==0){
                                Db::table('tbl_config')->where(['id'=>1])->update(['template_sign_zonea'=>$reqComponents['data'][0],'template_sign_zoneb'=>$reqComponents['data'][1],'template_sign_zonec'=>$reqComponents['data'][2],'template_sign_wz_a'=>$reqComponents['data'][3],'template_sign_wz_b'=>$reqComponents['data'][4],'template_sign_wz_c'=>$reqComponents['data'][5]]);
                                $this->success('上传成功', url('Contract/index'),$reqComponents['data']);
                            }else{
                                $this->error($reqComponents['message']);
                            }
                        }else{
                            $this->error($reqUpload['msg']);
                        }
                    }else{
                        $this->error($reqTemplate['message']);
                    }
                }else{
                    $this->error('上传失败！');
                }
            }else{
                throw new ApiException([
                    'msg' => '未上传文件',
                    'errorCode' => '10000'
                ]);
            }
        }
    }

    //上传委托书模板
    public function createEntrust(ContractModel $ContractModel,ContractRequest $request)
    {
        if($request->isPost()){
            if(!empty($_FILES) ){
                //移动到public/assets/uploads/目录下
                $info = move_uploaded_file($_FILES["file"]["tmp_name"],config('upload_file').$_FILES["file"]["name"]);
                if($info){
                    $filePath = config('upload_file').$_FILES["file"]["name"];
                    $fileName = $_FILES["file"]["name"];
                    //e签宝模板管理-通过上传方式创建模板
                    $reqTemplate=createByUploadUrl($filePath,$fileName);
                    if($reqTemplate['code']==0){
                        $fileName=explode(".",$fileName);
                        Db::table('tbl_config')->where(['id'=>2])->update(['template_id'=>$reqTemplate['data']['templateId'],'file_name'=>$fileName[0]]);
                        //e签宝文件流上传方法
                        $reqUpload=uploadUrl($filePath,$reqTemplate['data']['uploadUrl']);
                        if($reqUpload['errCode']==0){
                            sleep(10);
                            //e签宝添加输入项组件
                            $reqComponents=components($reqTemplate['data']['templateId']);
                            if($reqComponents['code']==0){
                                Db::table('tbl_config')->where(['id'=>2])->update(['template_sign_zonea'=>$reqComponents['data'][0],'template_sign_zoneb'=>$reqComponents['data'][1],'template_sign_zonec'=>$reqComponents['data'][2]]);
                                $this->success('上传成功', url('Contract/index'),$reqComponents['data']);
                            }else{
                                $this->error($reqComponents['message']);
                            }
                        }else{
                            $this->error($reqUpload['msg']);
                        }
                    }else{
                        $this->error($reqTemplate['message']);
                    }
                }else{
                    $this->error('上传失败！');
                }
            }else{
                throw new ApiException([
                    'msg' => '未上传文件',
                    'errorCode' => '10000'
                ]);
            }
        }
    }

    //生成电子公章
    public function createSeal(ContractRequest $request)
    {
        if($request->isAjax()){
            $data=Db::table('tbl_config')->field('account_id,org_id')->where('id',1)->find();
            if(empty($data['account_id'])){
                $thirdPartyUserId=config('e_accounts_thirdPartyUserId');
                $name=config('e_accounts_name');
                $mobile=config('e_accounts_mobile');
                $idNumber=config('e_accounts_idNumber');
                //e签宝个人账号创建
                $reqByThirdPartyUserId=createByThirdPartyUserId($thirdPartyUserId,$name,$mobile,$idNumber);
                if($reqByThirdPartyUserId['code']==0){
                    //更新平台端机构签署经办人
                    $updateAccountId=Db::table('tbl_config')->where(['id'=>1])->update(['account_id'=>$reqByThirdPartyUserId['data']['accountId']]);
                    if($updateAccountId){
                        $orgThirdPartyUserId=config('e_org_thirdPartyUserId');
                        $orgName=config('e_org_name');
                        $orgIdNumber=config('e_org_idNumber');
                        //e签宝机构账号创建
                        $reqOrgCreateByThirdPartyUserId=orgCreateByThirdPartyUserId($reqByThirdPartyUserId['data']['accountId'],$orgThirdPartyUserId,$orgName,$orgIdNumber);
                        if($reqOrgCreateByThirdPartyUserId['code']==0){
                            //更新平台端机构账号
                            $updateOrgId=Db::table('tbl_config')->where(['id'=>1])->update(['org_id'=>$reqOrgCreateByThirdPartyUserId['data']['orgId']]);
                            if($updateOrgId){
                                //e签宝创建机构模板印章
                                $reqOfficialtemplate=officialtemplate($reqOrgCreateByThirdPartyUserId['data']['orgId']);
                                if($reqOfficialtemplate['code']==0){
                                    //更新平台端印章id
                                    $updateSealId=Db::table('tbl_config')->where(['id'=>1])->update(['seal_id'=>$reqOfficialtemplate['data']['sealId']]);
                                    if($updateSealId){
                                        $this->success('生成成功', url('Contract/index'),$reqOfficialtemplate['data']);
                                    }else{
                                        $this->error("sealId更新失败！");
                                    }
                                }else{
                                    $this->error($reqOfficialtemplate['message']);
                                }
                            }else{
                                $this->error("orgId更新失败！");
                            }
                        }else{
                            $this->error($reqOrgCreateByThirdPartyUserId['message']);
                        }
                    }else{
                        $this->error("accountId更新失败！");
                    }
                }else{
                    $this->error($reqByThirdPartyUserId['message']);
                }
            }else{
                $this->success('已生成过电子公章！', url('Contract/index'));
            }
        }
    }

    //查看合同正文
    public function getDocuments(ContractRequest $request)
    {
        if($request->isPost()){
            $params = $this->request->param();
            $reqDocumentSignflows=documentSignflows($params['flowid']);
            if($reqDocumentSignflows['code']==0){
                $this->success('成功！', url('Contract/index'),$reqDocumentSignflows['data']['docs'][0]['fileUrl']);
            }else{
                $this->error($reqDocumentSignflows['message']);
            }
        }
    }

    //查看委托书
    public function getWts(ContractRequest $request)
    {
        if($request->isPost()){
            $params = $this->request->param();
            $reqDocumentSignflows=documentSignflows($params['flowid']);
            if($reqDocumentSignflows['code']==0){
                $this->success('成功！', url('Contract/ck'),$reqDocumentSignflows['data']['docs'][0]['fileUrl']);
            }else{
                $this->error($reqDocumentSignflows['message']);
            }
        }
    }

}