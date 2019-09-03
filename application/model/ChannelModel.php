<?php

namespace app\model;


class ChannelModel extends BaseModel
{
    protected $name = 'Channel';

    public function getChannelList($limit=10){
       return self::where(['is_delete'=>0])->paginate($limit, false, ['query' => request()->param()]);
    }
}