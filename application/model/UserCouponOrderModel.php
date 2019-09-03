<?php

namespace app\model;

use think\Db;

class UserCouponOrderModel extends BaseModel
{
    protected $name = 'user_coupon_order';

    //释放过期未支付订单库存
    public function releaseInventory(){
        $order=self::where(['state'=>0,'is_expiration'=>0]);
        $order= $order->where('expiration_time','<',time());
        $order=$order->select();
        Db::startTrans();
        try {
            foreach ($order as $k=>$v){
                self::where(['id'=>$v->id])->update(['is_expiration'=>1]);
                if($v['download_coupon_id']){
                    //释放下载券库存
                   DownloadCouponModel::where(['id'=>$v->download_coupon_id])->setInc('stock',$v->num);
                }else{
                    //释放自建券库存
                    ContractModel::where(['id'=>$v->coupon_id])->setInc('stock',$v->num);
                }
            }
            Db::commit();
            return $order;
        } catch (ApiException $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }catch(\Exception $e){
            Db::rollback();
            throw $e;
        }

    }

}