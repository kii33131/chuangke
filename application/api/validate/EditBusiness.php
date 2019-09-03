<?php
namespace app\api\validate;

class EditBusiness extends BaseValidate
{
    protected $rule = [
        //'type' => 'isNotEmpty|in:1,2',
        'name'=>'require|isNotEmpty',
        'abbreviation'=>'require|isNotEmpty',
        'taxpayer'=>'require|isNotEmpty',
        'license'=>'require|isNotEmpty',
        'address'=>'require|isNotEmpty',
        'contacts'=>'require|isNotEmpty',
        'contacts_mobile'=>'require|isNotEmpty|mobile',
        'business_mobile'=>'require|isNotEmpty|mobile',
        'industry_code'=>'require|isNotEmpty',
        'province'=>'require|isNotEmpty',
        'city'=>'require|isNotEmpty',
        'city_id'=>'require|isNotEmpty',
        'industry'=>'require|isNotEmpty',

    ];


}
