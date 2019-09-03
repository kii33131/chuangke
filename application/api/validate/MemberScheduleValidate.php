<?php
namespace app\api\validate;

class MemberScheduleValidate extends BaseValidate
{
    protected $rule = [
        'start_time' => 'require|isNotEmpty',
        'end_time' => 'require|isNotEmpty',//卡券名称
        'basis' => 'require|isNotEmpty',//原价
        'completion_degree' => 'require|isNotEmpty',//原价
        'member_task_id' => 'require|isNotEmpty',//原价
        'schedule_id' => 'require|isNotEmpty',//原价
        //member_task_id

    ];

    protected $scene = [
        'create' => ['start_time','end_time','basis','completion_degree','member_task_id'],
        'list' => ['member_task_id'],
        'schedule_id' => ['schedule_id'],

    ];

}
