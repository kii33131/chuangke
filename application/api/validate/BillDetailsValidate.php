<?php
namespace app\api\validate;

class BillDetailsValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isNotEmpty'
    ];

}
