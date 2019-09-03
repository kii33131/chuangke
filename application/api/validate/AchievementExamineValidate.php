<?php
namespace app\api\validate;

class AchievementExamineValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isNotEmpty',
        'type' => 'require|isNotEmpty',
    ];
}
