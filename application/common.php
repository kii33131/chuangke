<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 钩子行为
 */

use think\permissions\facade\Permissions;
use think\permissions\facade\Roles;

if (!function_exists('hook')) {
	function hook($behavior, $params) {
		\think\facade\Hook::exec($behavior, $params);
	}
}

/**
 * 编辑按钮
 */
if (!function_exists('editButton')) {
	function editButton(string $url, string $name = '编辑') {
		return sprintf('<a href="%s"><button class="btn btn-info btn-xs edit" type="button"><i class="fa fa-paste"></i> %s</button></a>', $url, $name);
	}
}

/**
 * 增加按钮
 */
if (!function_exists('createButton')) {
	function createButton(string $url, string $name, $isBig = true) {
		return $isBig ? sprintf('<a href="%s"> <button type="button" class="btn btn-w-m btn-primary"><i class="fa fa-check-square-o"></i> %s</button></a>', $url, $name) :
			sprintf('<a href="%s"> <button type="button" class="btn btn-xs btn-primary"><i class="fa fa-check-square-o"></i> %s</button></a>', $url, $name);
	}
}

/**
 * 删除按钮
 */
if (!function_exists('deleteButton')) {
	function deleteButton(string $url, int $id, string $name="删除") {
		return sprintf('<button class="btn btn-danger btn-xs delete" data-url="%s" data=%d type="button"><i class="fa fa-trash"></i> %s</button>', $url, $id, $name);
	}
}

/**
 * 通过按钮
 */
if (!function_exists('passButton')) {
	function passButton(string $url, int $id, string $name="通过",string $btn_size = 'btn-xs') {
		return sprintf('<button class="btn btn-success %s pass" data-url="%s" data=%d type="button">%s</button>',$btn_size, $url, $id, $name);
	}
}

/**
 * 拒绝按钮
 */
if (!function_exists('refuseButton')) {
	function refuseButton(string $url, int $id, string $name="拒绝",string $btn_size = 'btn-xs') {
		return sprintf('<button class="btn btn-warning %s refuse" data-url="%s" data=%d type="button">%s</button>',$btn_size, $url, $id, $name);
	}
}

/**
 * diy按钮
 */
if (!function_exists('diyButton')) {
    function diyButton(string $url, string $name = '') {
        return sprintf('<a href="%s"><button class="btn btn-info btn-xs" type="button">%s</button></a>', $url,$name);
    }
}

/**
 * 搜索按钮
 */
if (!function_exists('searchButton')) {
	function searchButton(string $name="搜索") {
		return sprintf('<button class="btn btn-white" type="submit"><i class="fa fa-search"></i> %s</button>', $name);
	}
}

/**
 * 生成密码
 */
if (!function_exists('generatePassword')) {
	function generatePassword(string  $password, int $algo = PASSWORD_DEFAULT) {
		return password_hash($password, $algo);
	}
}

/**
 * 权限判断
 * @param $permission
 * @return bool
 */
function can($permission)
{
    $module = request()->module();
    list($controller, $action) = explode('@', $permission);
    $user = request()->session(config('permissions.user'));
    $roleIDs = $user->getRoles(false);
    $permission = Permissions::getPermissionByModuleAnd($module, $controller, $action);
    if (!$permission) {
        return true;
    }
    $permissions = [];
    foreach ($roleIDs as $role) {
        if($role == 1){
            return true;
        }
        $permissions = array_merge($permissions, (Roles::getRoleBy($role)->getPermissions(false)));
    }
    if (!in_array($permission->id, $permissions)) {
        return false;
    }
    return true;
}

/**
 * 生成随机字符串
 * @param $length
 * @return string|null
 */
function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

/**
 * api成功返回数据
 * @param $data
 * @throws \app\exceptions\SuccessMessage
 */
function success($data = []){
    throw new \app\exceptions\SuccessMessage([
        'data' => $data
    ]);
}

/**
 * api失败返回数据
 * @param $msg
 * @param $errorCode
 * @param int $code
 * @throws \app\exceptions\ApiException
 */
function error($msg,$errorCode,$code = 400){
    throw new \app\exceptions\ApiException([
        'msg' => $msg,
        'errorCode' => $errorCode,
        'code' => $code
    ]);
}

//1.e签宝获取鉴权Token
function e_access_token() {
    $url=config('e_url')."/v1/oauth2/access_token?appId=".config('e_appId')."&secret=".config('e_secret')."&grantType=".config('e_grantType');
    $data="";
    $request=curlRequest($url,$data);
    return $request['data']['token'];
}
//2.e签宝模板管理-通过上传方式创建模板
function createByUploadUrl($filePath,$fileName) {
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/docTemplates/createByUploadUrl";
    $md5file = md5_file($filePath,true);
    $contentMd5=base64_encode($md5file);
    $contentType="application/octet-stream";
    $data=array(
        'contentMd5'=>$contentMd5,
        'contentType'=>$contentType,
        'fileName'=>$fileName,
        'convert2Pdf'=>true
    );
    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//3.e签宝文件流上传方法
function uploadUrl($filePath,$uploadUrl) {
    $appId=config('e_appId');
    $access_token=e_access_token();
    $md5file = md5_file($filePath,true);
    $contentMd5=base64_encode($md5file);
    $contentType="application/octet-stream";
    $PSize = filesize($filePath);
    $data = fread(fopen($filePath, "r"), $PSize); //文件转换成二进制流
    $request1=putcurlRequestJson($uploadUrl,$data,$appId,$access_token,$contentMd5,$contentType);
    return $request1;
}
//4.e签宝添加输入项组件
function components($templateId){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/docTemplates/".$templateId."/components";
    $data=array(
        'structComponent'=>array(
            array(
                'type'=>6,
                'context'=>array(
                    'label'=>"平台盖章区",
                    'required'=>true,
                    'style'=>array(
                        'width'=>config('e_width'),
                        'height'=>config('e_height')
                    ),
                    'pos'=>array(
                        'page'=>config('e_page'),
                        'x'=>config('e_pt_x'),
                        'y'=>config('e_pt_y')
                    )
                )
            ),
            array(
                'type'=>6,
                'context'=>array(
                    'label'=>"企业盖章区",
                    'required'=>true,
                    'style'=>array(
                        'width'=>config('e_width'),
                        'height'=>config('e_height')
                    ),
                    'pos'=>array(
                        'page'=>config('e_page'),
                        'x'=>config('e_ql_x'),
                        'y'=>config('e_ql_y')
                    )
                )
            ),
            array(
                'type'=>6,
                'context'=>array(
                    'label'=>"创客盖章区",
                    'required'=>true,
                    'style'=>array(
                        'width'=>config('e_width'),
                        'height'=>config('e_height')
                    ),
                    'pos'=>array(
                        'page'=>config('e_page'),
                        'x'=>config('e_ck_x'),
                        'y'=>config('e_ck_y')
                    )
                )
            ),
            array(
                'type'=>1,
                'context'=>array(
                    'label'=>"平台填写文字",
                    'required'=>true,
                    'style'=>array(
                        'width'=>config('e_wz_width'),
                        'height'=>config('e_height'),
                        'font'=>1,
                        'fontSize'=>22
                    ),
                    'pos'=>array(
                        'page'=>config('e_wz_page'),
                        'x'=>config('e_pt_wz_x'),
                        'y'=>config('e_pt_wz_y')
                    )
                )
            ),
            array(
                'type'=>1,
                'context'=>array(
                    'label'=>"企业填写文字",
                    'required'=>true,
                    'style'=>array(
                        'width'=>config('e_wz_width'),
                        'height'=>config('e_height'),
                        'font'=>1,
                        'fontSize'=>22
                    ),
                    'pos'=>array(
                        'page'=>config('e_wz_page'),
                        'x'=>config('e_ql_wz_x'),
                        'y'=>config('e_ql_wz_y')
                    )
                )
            ),
            array(
                'type'=>1,
                'context'=>array(
                    'label'=>"创客填写文字",
                    'required'=>true,
                    'style'=>array(
                        'width'=>config('e_wz_width'),
                        'height'=>config('e_height'),
                        'font'=>1,
                        'fontSize'=>22
                    ),
                    'pos'=>array(
                        'page'=>config('e_wz_page'),
                        'x'=>config('e_ck_wz_x'),
                        'y'=>config('e_ck_wz_y')
                    )
                )
            )
        )
    );
    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//5.e签宝查询模板详情
function docTemplates($templateId){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/docTemplates/".$templateId;
    $request=getcurlRequest($url,$appId,$access_token);
    return $request;
}
//6.e签宝签署流程创建 1合同 2委托书f
function signflows($businessScene,$type=1){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows";
    if($type==1){
        $data=array(
            "autoArchive"=>true,
            'businessScene'=>$businessScene,
            'configInfo'=>array(
                'noticeDeveloperUrl'=>config('api_url_domain_root_i')."/api/user/redirects",//
                'noticeType'=>" ",
                'redirectUrl'=>config('url_domain_root_i'),
                'signPlatform'=>"1"
            )
        );
    }elseif($type==2){
        $data=array(
            "autoArchive"=>true,
            'businessScene'=>$businessScene,
            'configInfo'=>array(
                'noticeDeveloperUrl'=>config('api_url_domain_root_i')."/api/user/redirecty",
                'noticeType'=>" ",
                'redirectUrl'=>config('url_domain_root_i'),
                'signPlatform'=>"1"
            )
        );
    }
    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//7.e签宝签署流程查询
function signflowsshow($flowId){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows/".$flowId;
    $request=getcurlRequest($url,$appId,$access_token);
    return $request;
}
//8.e签宝通过模板创建文件 1合同 2委托书
function createByTemplate($templateId,$name,$template_sign_zonea,$template_sign_zoneb,$template_sign_zonec,$type=1,$template_sign_wz_a='',$template_sign_wz_b='',$template_sign_wz_c='',$template_sign_wz_awz='',$template_sign_wz_bwz='',$template_sign_wz_cwz=''){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/files/createByTemplate";
    if($type==1){
        $data=array(
            'name'=>$name,
            'templateId'=>$templateId,
            'simpleFormFields'=>array(
                $template_sign_zonea=>"平台盖章区",
                $template_sign_zoneb=>"创客盖章区",
                $template_sign_zonec=>"企业盖章区",
                $template_sign_wz_a=>$template_sign_wz_awz,
                $template_sign_wz_b=>$template_sign_wz_bwz,
                $template_sign_wz_c=>$template_sign_wz_cwz
            )
        );
    }elseif($type==2){
        $data=array(
            'name'=>$name,
            'templateId'=>$templateId,
            'simpleFormFields'=>array(
                $template_sign_zonea=>"平台盖章区",
                $template_sign_zoneb=>"创客盖章区",
                $template_sign_zonec=>"企业盖章区"
            )
        );
    }

    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//9.e签宝流程文档添加
function documents($flowId,$fileId){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows/".$flowId."/documents";
    $data=array(
        'docs'=>array(
            array(
                'fileId'=>$fileId
            )
        )
    );
    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//10.1.e签宝个人账号创建
function createByThirdPartyUserId($thirdPartyUserId,$name,$mobile,$idNumber)
{
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/accounts/createByThirdPartyUserId";
    $data=array(
        'thirdPartyUserId'=>$thirdPartyUserId,
        'name'=>$name,
        'idType'=>'CRED_PSN_CH_IDCARD',
        'idNumber'=>$idNumber,
        'mobile'=>$mobile
    );
    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//10.2.e签宝机构账号创建
function orgCreateByThirdPartyUserId($accountId,$thirdPartyUserId,$name,$idNumber)
{
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/organizations/createByThirdPartyUserId";
    $data=array(
        'thirdPartyUserId'=>$thirdPartyUserId,
        'creator'=>$accountId,
        'idType'=>'CRED_ORG_USCC',
        'idNumber'=>$idNumber,
        'name'=>$name
    );
    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//10.3.e签宝创建机构模板印章
function officialtemplate($orgId)
{
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/organizations/".$orgId."/seals/officialtemplate";
    $data=array(
        'color'=>"RED",
        'type'=>"TEMPLATE_ROUND",
        'central'=>"STAR"
    );
    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//11.e签宝添加手动盖章签署区 1合同 2委托书
function handSign($flowId,$fileId,$accountId,$posX,$posY,$orgId='',$sealId='',$type=1){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows/".$flowId."/signfields/handSign";
    if($type==1){
        if(!$orgId){
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'assignedPosbean'=>true,
                        'signerAccountId'=>$accountId,
                        'posBean'=>array(
                            'posPage'=>config('e_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }else{
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'assignedPosbean'=>true,
                        'signerAccountId'=>$accountId,
                        'authorizedAccountId'=>$orgId,
                        'actorIndentityType'=>2,
                        'sealId'=>$sealId,
                        'posBean'=>array(
                            'posPage'=>config('e_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }
    }elseif($type==2){
        if(!$orgId){
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'assignedPosbean'=>true,
                        'signerAccountId'=>$accountId,
                        'posBean'=>array(
                            'posPage'=>config('e_wt_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }else{
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'assignedPosbean'=>true,
                        'signerAccountId'=>$accountId,
                        'authorizedAccountId'=>$orgId,
                        'actorIndentityType'=>2,
                        'sealId'=>$sealId,
                        'posBean'=>array(
                            'posPage'=>config('e_wt_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }
    }

    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}

//11.2.e签宝添加平台自动盖章签署区 1合同 2委托书
function platformSign($flowId,$fileId,$posX,$posY,$sealId,$type=1){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows/".$flowId."/signfields/platformSign";
    if($type==1){
        $data=array(
            'signfields'=>array(
                array(
                    'fileId'=>$fileId,
                    'sealId'=>$sealId,
                    'posBean'=>array(
                        'posPage'=>config('e_page'),
                        'posX'=>$posX,
                        'posY'=>$posY
                    )
                )
            )
        );
    }elseif($type==2){
        $data=array(
            'signfields'=>array(
                array(
                    'fileId'=>$fileId,
                    'sealId'=>$sealId,
                    'posBean'=>array(
                        'posPage'=>config('e_wt_page'),
                        'posX'=>$posX,
                        'posY'=>$posY
                    )
                )
            )
        );
    }

    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}

//11.3.e签宝添加签署方自动盖章签署区 1合同 2委托书
function autoSign($flowId,$fileId,$accountId,$posX,$posY,$orgId='',$sealId='',$type=1){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows/".$flowId."/signfields/autoSign";
    if($type==1){
        if(!$orgId){
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'authorizedAccountId'=>$accountId,
                        'sealId'=>$sealId,
                        'posBean'=>array(
                            'posPage'=>config('e_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }else{
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'authorizedAccountId'=>$orgId,
                        'sealId'=>$sealId,
                        'posBean'=>array(
                            'posPage'=>config('e_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }
    }elseif($type==2){
        if(!$orgId){
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'authorizedAccountId'=>$accountId,
                        'sealId'=>$sealId,
                        'posBean'=>array(
                            'posPage'=>config('e_wt_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }else{
            $data=array(
                'signfields'=>array(
                    array(
                        'fileId'=>$fileId,
                        'authorizedAccountId'=>$orgId,
                        'sealId'=>$sealId,
                        'posBean'=>array(
                            'posPage'=>config('e_wt_page'),
                            'posX'=>$posX,
                            'posY'=>$posY
                        )
                    )
                )
            );
        }
    }

    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}

//11.4.e签宝静默签署授权
function signAuth($accountId){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signAuth/".$accountId;
    $data=array();

    $request=curlRequestJson($url,$data,$appId,$access_token);
    return $request;
}

//12.e签宝签署流程开启
function start($flowId)
{
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows/".$flowId."/start";
    $data='';
    $request=getputcurlRequestJson($url,$data,$appId,$access_token);
    return $request;
}
//13.e签宝获取签署地址
function executeUrl($flowId,$accountId,$organizeId)
{
    $appId=config('e_appId');
    $access_token=e_access_token();
    if($organizeId){
        $url=config('e_url')."/v1/signflows/".$flowId."/executeUrl?accountId=".$accountId."&organizeId=".$organizeId;
    }else{
        $url = config('e_url')."/v1/signflows/" . $flowId . "/executeUrl?accountId=" . $accountId;
    }
    $request = getcurlRequest($url, $appId, $access_token);
    return $request;
}

//14.e签宝流程文档下载
function documentSignflows($flowId){
    $appId=config('e_appId');
    $access_token=e_access_token();
    $url=config('e_url')."/v1/signflows/".$flowId."/documents";
    $request=getcurlRequest($url,$appId,$access_token);
    return $request;
}

/**
使用curl方式实现get或post请求,一般数据格式
@param $url 请求的url地址
@param $data 发送的post数据 如果为空则为get方式请求
return 请求后获取到的数据
 **/
function curlRequest($url,$data){
    $ch = curl_init();
    $params[CURLOPT_URL] = $url;    //请求url地址
    $params[CURLOPT_HEADER] = false; //是否返回响应头信息
    $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
    $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
    $params[CURLOPT_TIMEOUT] = 30; //超时时间
    if(!empty($data)){
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $data;
    }
    $params[CURLOPT_SSL_VERIFYPEER] = false;//请求https时设置,还有其他解决方案
    $params[CURLOPT_SSL_VERIFYHOST] = false;//请求https时,其他方案查看其他博文
    curl_setopt_array($ch, $params); //传入curl参数
    $content = curl_exec($ch); //执行
    if (curl_errno($ch)) {
        $error = curl_error($ch);
    }
    curl_close($ch); //关闭连接
    return json_decode($content,true);
}

/**
使用curl方式实现post请求,json数据格式
@param $url 请求的url地址
@param $data 发送的post数据
return 请求后获取到的数据
 */
function curlRequestJson($url,$data,$appId,$access_token){
    $data=json_encode($data);
    $ch = curl_init($url); //初始化
    curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //请求https时设置,还有其他解决方案
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //请求https时,其他方案查看其他博文
    curl_setopt($ch, CURLOPT_POST, 1); //设置post方式提交
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //Post提交的数据包
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json;charset=UTF-8',
        'Content-Length: ' . strlen($data),
        'X-Tsign-Open-App-Id: ' . $appId,
        'X-Tsign-Open-Token: ' . $access_token
    ));
    $contents = curl_exec($ch); //执行并存储结果
    if (curl_errno($ch)) {
        print curl_error($ch);
    }
    curl_close($ch); //关闭URL请求
    return json_decode($contents,true);
}

/**
使用curl方式实现put请求,json数据格式
@param $url 请求的url地址
@param $data 发送的post数据
return 请求后获取到的数据
 */
function putcurlRequestJson($url,$data,$appId,$access_token,$contentMd5,$contentType){
    $ch = curl_init($url); //初始化
    curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //请求https时设置,还有其他解决方案
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //请求https时,其他方案查看其他博文
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT"); //设置请求方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //Post提交的数据包
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Tsign-Open-App-Id: ' . $appId,
        'X-Tsign-Open-Token: ' . $access_token,
        'Content-MD5: ' . $contentMd5,
        'Content-Type: ' . $contentType
    ));
    $contents = curl_exec($ch); //执行并存储结果
    if (curl_errno($ch)) {
        print curl_error($ch);
    }
    curl_close($ch); //关闭URL请求
    return json_decode($contents,true);
}

/**
使用curl方式实现put请求,json数据格式
@param $url 请求的url地址
@param $data 发送的post数据
return 请求后获取到的数据
 */
function getputcurlRequestJson($url,$data,$appId,$access_token){
    $ch = curl_init($url); //初始化
    curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //请求https时设置,还有其他解决方案
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //请求https时,其他方案查看其他博文
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT"); //设置请求方式
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Tsign-Open-App-Id: ' . $appId,
        'X-Tsign-Open-Token: ' . $access_token
    ));
    $contents = curl_exec($ch); //执行并存储结果
    if (curl_errno($ch)) {
        print curl_error($ch);
    }
    curl_close($ch); //关闭URL请求
    return json_decode($contents,true);
}

/**
使用curl方式实现get请求,一般数据格式
@param $url 请求的url地址
return 请求后获取到的数据
 */
function getcurlRequest($url,$appId,$access_token){
    $ch = curl_init($url); //初始化
    curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //请求https时设置,还有其他解决方案
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //请求https时,其他方案查看其他博文
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Tsign-Open-App-Id: ' . $appId,
        'X-Tsign-Open-Token: ' . $access_token
    ));
    $contents = curl_exec($ch); //执行并存储结果
    if (curl_errno($ch)) {
        print curl_error($ch);
    }
    curl_close($ch); //关闭URL请求
    return json_decode($contents,true);
}