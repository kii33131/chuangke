<?php
namespace app\api\validate;

class ManagementListValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'status|isNotEmpty|in:1,2,3',

    ];

}
