<?php

namespace app\model;

class DistrictModel extends BaseModel
{
    protected $name = 'district';

    public function addressList(){

      return  self::where(['type'=>1])
          ->field('id,district_name')
          ->paginate(1000000, false, ['query' => request()->param()])->each(function ($item){
               $item->son = self::where(['pid'=>$item->id])->select()->each(function ($items){
               });
          });
    }
}