<?php
namespace app\api\validate;

class SettlementSubmissionValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isNotEmpty',
        'money' => 'require|isNotEmpty',
        'tax_type' => 'require|isNotEmpty|in:1,2',
        'service_pay_type' => 'require|isNotEmpty|in:1,2,3',
        'invoice_type' => 'require|isNotEmpty|in:1,2',
        'accounting_subjects' => 'require|isNotEmpty',
    ];

}
