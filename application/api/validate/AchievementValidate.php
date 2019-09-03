<?php
namespace app\api\validate;

class AchievementValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isNotEmpty',

    ];

}
