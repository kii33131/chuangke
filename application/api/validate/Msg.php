<?php
namespace app\api\validate;

class Msg extends BaseValidate
{
    protected $rule = [
        'type' => 'isNotEmpty|in:1,2',//1创客登录 2企业登录
        'mobbile'=>'isNotEmpty'
    ];


}
