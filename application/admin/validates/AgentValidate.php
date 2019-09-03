<?php

namespace app\admin\validates;

class AgentValidate extends AbstractValidate
{
    protected $rule = [
        'phone' => 'require|mobile',
        'name' => 'require',//
        'account' => 'require',//
        'pumping_ratio' => 'require|percentage',//
        'residence_rebate' => 'require|percentage',//
    ];


    protected function mobile($phone){
        $check = '/^1\d{10}$/';
        if (!preg_match($check, $phone)) {
            return '手机号码不符合格式';
        } else {
            return true;
        }

    }
}