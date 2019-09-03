<?php

namespace app\model;

class BannerModel extends BaseModel
{
    protected $name = 'banner';
    protected $type = [
        'imgs'    =>  'json',
    ];
    public function get_banner($city){
        $list = self::where(['state'=>1,'city'=>$city])->field('id,title,img1,img2,img3,url')->order('id', 'desc')->select();
        return $list;
    }

    public function getAllList($params, $limit = self::LIMIT)
    {
        $banner = $this->where([]);
        if(!empty($params['province'])) {
            $banner = $banner->where([
                'province' => $params['province'],
                'city' => $params['city'],
                'district' => $params['district']
            ]);
        }
        return $banner->order('id desc')->paginate($limit, false, ['query' => request()->param()]);
    }
}