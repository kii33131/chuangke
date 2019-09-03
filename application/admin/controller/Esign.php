<?php

namespace app\admin\controller;

use app\model\EsignModel;
use app\admin\request\EsignRequest;
use think\Db;

class Esign extends Base
{
	/**
	 * User List
	 *
	 * @time at 2018年11月12日
	 * @return mixed|string
	 */
	public function index(EsignModel $EsignModel)
	{
		$params = $this->request->param();
		$this->checkParams($params);
		$this->esigns = $EsignModel->getList($params, $this->limit);

		return $this->fetch();
	}

	/**
	 * create Data
	 *
	 * @time at 2018年11月12日
	 * @return mixed|string
	 */
	public function create(EsignModel $EsignModel, EsignRequest $request)
	{
		if ($request->isPost()) {
			$data = $request->post();
			if($data['is_default']){
                Db::table('tbl_esign')->where('is_default', 1)->update(['is_default'=>0]);
            }
			if ($esignId = $EsignModel->store($data)) {
                $esignData=Db::table('tbl_esign')->field('*')->where('id',$esignId)->find();
                 $thirdPartyUserId=$esignData['accounts_thirdpartyuserId'];
                 $name=$esignData['accounts_name'];
                 $mobile=$esignData['accounts_mobile'];
                 $idNumber=$esignData['accounts_idnumber'];
                 //e签宝个人账号创建
                 $reqByThirdPartyUserId=createByThirdPartyUserId($thirdPartyUserId,$name,$mobile,$idNumber);
                 if($reqByThirdPartyUserId['code']==0){
                     //更新平台端机构签署经办人
                     $updateAccountId=Db::table('tbl_esign')->where(['id'=>$esignId])->update(['account_id'=>$reqByThirdPartyUserId['data']['accountId']]);
                     if($updateAccountId){
                         $orgThirdPartyUserId=$esignData['org_thirdpartyuserId'];
                         $orgName=$esignData['org_name'];
                         $orgIdNumber=$esignData['org_idnumber'];
                         //e签宝机构账号创建
                         $reqOrgCreateByThirdPartyUserId=orgCreateByThirdPartyUserId($reqByThirdPartyUserId['data']['accountId'],$orgThirdPartyUserId,$orgName,$orgIdNumber);
                         if($reqOrgCreateByThirdPartyUserId['code']==0){
                             //更新平台端机构账号
                             $updateOrgId=Db::table('tbl_esign')->where(['id'=>$esignId])->update(['org_id'=>$reqOrgCreateByThirdPartyUserId['data']['orgId']]);
                             if($updateOrgId){
                                 //e签宝创建机构模板印章
                                 $reqOfficialtemplate=officialtemplate($reqOrgCreateByThirdPartyUserId['data']['orgId']);
                                 if($reqOfficialtemplate['code']==0){
                                     //更新平台端印章id
                                     $updateSealId=Db::table('tbl_esign')->where(['id'=>$esignId])->update(['seal_id'=>$reqOfficialtemplate['data']['sealId']]);
                                     if($updateSealId){
                                         $this->success('添加成功', url('esign/index'),$reqOfficialtemplate['data']);
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
			}
			$this->error('平台信息添加失败');
		}

		return $this->fetch();
	}

	/**
	 * Edit Data
	 *
	 * @time at 2018年11月12日
	 * @return mixed|string
	 */
	public function edit(EsignModel $EsignModel, EsignRequest $request)
	{
		if ($request->isPost()) {
			$data = $request->post();
            if($data['is_default']){
                Db::table('tbl_esign')->where('is_default', 1)->update(['is_default'=>0]);
            }
			$EsignModel->updateBy($data['id'], $data) ? $this->success('修改成功', url('esign/index')) : $this->error('修改失败');
		}

		$id = $this->request->param('id');
		if (!$id) {
			$this->error('数据不存在');
		}
		$esign = $EsignModel->findBy($id);

		$this->esign   =  $esign;
		return $this->fetch();
	}

	/**
	 * Delete Data
	 *
	 * @time at 2018年11月12日
	 * @return void
	 */
	public function delete(EsignModel $esignModel)
	{
		$id = $this->request->post('id');

		if (!$id) {
			$this->error('不存在的数据');
		}
		// 删除用户
		if ($esignModel->deleteBy($id)) {
			$this->success('删除成功', url('esign/index'));
		}
		$this->error('删除失败');
	}
}