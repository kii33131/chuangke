<?php
namespace app\api\validate;

class Task extends BaseValidate
{
    protected $rule = [
        'name' => 'require|isNotEmpty',
        'explain' => 'require|isNotEmpty',//
        'enclosure' => 'require|isNotEmpty',//
        'push_type' => 'require|isNotEmpty|in:1,2,3',
        'id' => 'require|isNotEmpty',
        'task_id' => 'require|isNotEmpty',
        'member_task_id' => 'require|isNotEmpty',
        'type' => 'require|isNotEmpty|in:1,2',
        'enclosure'=>'',
        'ids'=>'require|isNotEmpty',
         'type' => 'require|isNotEmpty|in:1,2,3',

        //'result_ids' => 'require|isNotEmpty',1


    ];
    protected $scene = [
        'release' => ['name','explain','push_type','result_ids'],
        'memberList' => ['id'],
        'detail' => ['id'],
        'receivingtask' => ['task_id'],
        'examine' => ['member_task_id','type'],
        'dealing_invitations' => ['member_task_id','type'],
        'lowerShelf' => ['ids','type'],

    ];


}
