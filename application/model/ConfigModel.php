<?php

namespace app\model;

class ConfigModel extends BaseModel
{
    protected $name = 'config';

    static public function getConfigs(){
       return self::get(1);
    }

    static public function getConfigs2(){
        return self::get(2);
    }

    static public function getConfigsByAccount($account_id){
        return self::where(['account_id'=>$account_id])->find();
    }
}