<?php

namespace app\model;

class UserDownloadCouponModel extends BaseModel
{
    protected $name = 'user_download_coupon';

    /**
     * 获取用户下载的卡券列表
     * @param $params
     * @param $uid
     * @param $listRows
     * @return \think\db\Query|\think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getListbyUser($params,$uid,$listRows){
        $EARTH=6378.137; //地球半径
        $PI=3.1415926535898; //PI值
        $coupons = self::alias('d')
            ->join('coupon c','d.coupon_id=c.id')->join('store s','c.store_id=s.id')
            ->where('d.user_id',$uid)
            ->where('d.is_delete',0);
        //判断卡券类型
        if(isset($params['type'])){
            $coupons = $coupons->where('c.type',$params['type']);
        }

        //筛选可使用卡券
        if(!empty($params['use']) && $params['use'] == 1){
            //筛选可使用卡券
            $coupons = $coupons->where([
                ['c.state','=',3],
                ['c.end_time' ,'>=',time()],
                ['d.stock' ,'>',0]
            ]);
        }else{
            //判断券是否过期
            if(isset($params['is_overdue'])){
                if($params['is_overdue'] == 1){//未过期
                    $coupons = $coupons->where('c.end_time','>=',time());
                }elseif ($params['is_overdue'] == 2){//已过期
                    $coupons = $coupons->where('c.end_time','<',time());
                }
            }
        }
        $field_distance = ",(2 * {$EARTH}* ASIN(SQRT(POW(SIN($PI*(".$params['latitude']."-latitude)/360),2)+COS($PI*".$params['latitude']."/180)* COS(latitude * $PI/180)*POW(SIN($PI*(".$params['longitude']."-longitude)/360),2)))) as distance";

        $coupons = $coupons->field("d.id,c.id coupon_id,c.name,c.logo,s.name store_name,d.stock,c.original_price,c.buying_price,c.rebate_commission,c.type{$field_distance}")
            ->order('d.id desc')
            ->paginate($listRows)
            ->each(function ($item){
                $item->distance = round($item->distance,3);
            });

        return $coupons;

    }
}