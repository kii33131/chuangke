<?php
namespace app\api\validate;

class ContentValidate extends BaseValidate
{
    protected $rule = [
        'content' => 'require|isNotEmpty',
        'img' => 'require|isNotEmpty',
    ];

}
