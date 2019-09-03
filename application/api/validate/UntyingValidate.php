<?php
namespace app\api\validate;

class UntyingValidate extends BaseValidate
{
    protected $rule = [
        'mobile' => 'require|isNotEmpty|mobile',
        'code' => 'require|isNotEmpty',
    ];

}
