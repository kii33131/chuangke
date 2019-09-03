<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/12 0012
 * Time: 下午 16:38
 */

namespace app\admin\validates;

class EsignValidate extends AbstractValidate
{
	protected  $rule = [
		'accounts_thirdpartyuserId|用户唯一标识'   => 'require|alphaNum|unique:esign',
		'accounts_name|姓名'    => 'require',
        'accounts_mobile|手机号码'    => 'require|mobile',
        'accounts_idnumber|身份证件号'    => 'require|idCard',
        'org_thirdpartyuserId|机构唯一标识'    => 'require|alphaNum|unique:esign',
        'org_name|机构名称'    => 'require',
        'org_idnumber|企业统一信用证件号'    => 'require',
	];
}