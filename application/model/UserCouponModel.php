<?php

namespace app\model;

class UserCouponModel extends BaseModel
{
    protected $name = 'user_coupon';

    //获取下载券领取和核销列表
    public function getDownloadCouponReciveList($params,$limit = self::LIMIT,$user_id){

        $downloadcoupon=DownloadCouponModel::where(['coupon_id'=>$params['id'],'user_id'=>$user_id])->find();
        if(!$downloadcoupon){
            try{
                exception('下载卡券不存在','40001');
            }catch (\Exception $e){
                error($e->getMessage(),$e->getCode());
            }
        }
        $coupons = $this->alias('u')->join('user_download_coupon d','d.id=u.download_coupon_id')->join('member m','m.id=u.user_id');
        if($params['pull_type']==3){
            $coupons = $coupons->where('u.state',2);
        }
        $coupons = $coupons->where('u.download_coupon_id',$downloadcoupon->id);

        $data= $coupons->field('m.picture portrait,m.name ,u.created_at as date_time')->order('date_time','desc')->paginate($limit);

        return $data;
    }
    //获取自己创建的券领取和核销列表
    public function getCouponReciveList($params,$limit = self::LIMIT,$store_id){
        $coupon=ContractModel::where(['id'=>$params['id'],'store_id'=>$store_id])->find();
        if(!$coupon){
            try{
                exception('这不是你的卡券','40001');
            }catch (\Exception $e){
                error($e->getMessage(),$e->getCode());
            }
        }
        $coupons = $this->alias('u')->join('member m','m.id=u.user_id');
        if($params['pull_type']==3){
            $coupons = $coupons->where('u.state',2);
        }
        $coupons = $coupons->where('u.coupon_id',$params['id']);
        $data= $coupons->field('m.picture portrait,m.name ,u.created_at as date_time')->order('date_time','desc')->paginate($limit);
        return $data;
    }

    /**
     * 领取卡券
     * @param $userId 用户ID
     * @param $couponId 卡券ID
     * @param int $num 领取数量
     * @param int $downloadCouponId 下载券ID
     */
    public static function receiveCoupon($userId,$couponId,$num = 1,$downloadCouponId = 0){
        for ($i = 0;$i < $num;$i++){
            self::create([
                'user_id' => $userId,
                'coupon_id' => $couponId,
                'download_coupon_id' => $downloadCouponId,
                'state' => 1
            ]);
        }
    }
}