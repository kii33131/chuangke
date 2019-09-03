<?php
namespace app\api\validate;

class Business extends BaseValidate
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
        'legal_person'=>'require|isNotEmpty',
        'legal_person_id'=>'require|isNotEmpty',
        'id_img_frontal'=>'require|isNotEmpty',
        'id_img_back'=>'require|isNotEmpty',
        'bank_name'=>'require|isNotEmpty',
        'subbranch_name'=>'require|isNotEmpty',
        'bank_address'=>'require|isNotEmpty',
        'open_permit'=>'require|isNotEmpty',
        'status'=>'isNotEmpty|in:1,2',
        'card_number'=>'require|isNotEmpty',
        'industry_code'=>'require|isNotEmpty',
        'province'=>'require|isNotEmpty',
        'city'=>'require|isNotEmpty',
        'city_id'=>'require|isNotEmpty',
        'industry'=>'require|isNotEmpty',


        //'invoice_type'=>'isNotEmpty|in:1,2',

    ];


}
