<?php
namespace app\api\validate;

class UserSms extends BaseValidate
{
    protected $rule = [
        'type' => 'require|isNotEmpty|in:1,2,3,4',//
        'mobile'=>'require|isNotEmpty|mobile'
    ];

}
