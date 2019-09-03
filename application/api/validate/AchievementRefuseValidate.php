<?php
namespace app\api\validate;

class AchievementRefuseValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isNotEmpty',
        'option' => 'require|isNotEmpty',
    ];

}
