<?php
namespace app\api\validate;

class User extends BaseValidate
{
    protected $rule = [
        'mobile' => 'require|isNotEmpty|mobile',
        'code' => 'require|isNotEmpty',
        'type' => 'require|isNotEmpty|in:1,2',
        'sub_branch' => 'require|isNotEmpty',
        'bank_name' => 'require|isNotEmpty',
        'number' => 'require|isNotEmpty',
        'address' => 'require|isNotEmpty',
        'name' => 'require|isNotEmpty',
        'cart_id' => 'require|isNotEmpty',
        'id_img_frontal' => 'require|isNotEmpty',
        'id_img_back' => 'require|isNotEmpty',
        'channelids' => 'require|isNotEmpty',
        'business_id' =>'isNotEmpty',
        'hand_held_certificate' =>'require',
        'province' =>'require|isNotEmpty',
        'city' =>'require|isNotEmpty',
        'province_id' =>'require|isNotEmpty',
        'city_id' =>'require|isNotEmpty',

    ];
    //18966553322
    protected $scene = [
        'login'=>['mobile','code','type','business_id'],
        'bank'=>['sub_branch','bank_name','number','address'],
        'examine'=>['address','name','cart_id','id_img_frontal','id_img_back','channelids','hand_held_certificate','city','province_id','city_id','province'],
    ];

}
