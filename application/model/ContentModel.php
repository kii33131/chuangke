<?php

namespace app\model;

class ContentModel extends BaseModel
{
    protected $name = 'content';

    public function contentList($user_id,$data,$limit){
        if(isset($data['id']) && $data['id']){
            $user_id = $data['id'];
        }
        return self::where(['member_id'=>$user_id])->order('id desc')
              ->paginate($limit, false, ['query' => request()->param()])->each(function ($item){
                $item->img =\GuzzleHttp\json_decode($item->img);
            });
    }
}