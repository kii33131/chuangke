<?php
namespace app\api\validate;

class DopayValidate extends BaseValidate
{
    protected $rule = [
        'voucher' => 'require|isNotEmpty',
        'bank_id' => 'require|isNotEmpty',
        'achievement_id' => 'require|isNotEmpty',
    ];

}
