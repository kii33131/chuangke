<?php


namespace app\wxpay\controller;


use app\model\ConfigModel;
use app\model\IntegralOrderModel;
use app\model\IntegralRecordModel;
use app\model\MemberModel;
use app\model\StoreModel;
use app\model\UserCouponModel;
use app\model\UserCouponOrderModel;
use EasyWeChat\Factory;
use think\Db;

class Notify
{
    protected $config;
    public function __construct()
    {
        $this->config = [
            // 必要配置
            'app_id'             => ConfigModel::getParam('appid'),
            'mch_id'             => ConfigModel::getParam('mch_id'),
            'key'                => ConfigModel::getParam('pay_key'),   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => '', // XXX: 绝对路径！！！！
            'key_path'           => '',      // XXX: 绝对路径！！！！

            'notify_url'         => '',     // 你也可以在下单时单独设置来想覆盖它
        ];
        $this->app =  Factory::payment($this->config);

    }

    /**
     * 商家入驻
     */
    public function settledIn(){
        $response = $this->app->handlePaidNotify(function ($message, $fail) {
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    Db::startTrans();
                    try{
                        $store = StoreModel::lock(true)->where([
                            'order_no'=>$message['out_trade_no'],
                            'is_pay' => 0
                        ])->find();
                        if($store){
                            $store->is_pay = 1;
                            $store->save();
                            //入驻赠送积分
                            if(ConfigModel::getParam('entry_gift_points') > 0){
                                MemberModel::changeIntegral(
                                    MemberModel::getUidByStoreId($store->id),
                                    ConfigModel::getParam('entry_gift_points'),
                                    1,
                                    4
                                );
                            }
                            Db::commit();
                            return true;
                        }else{
                            exception('订单不存在！');
                        }
                    }catch (\Exception $e){
                        Db::rollback();
                        return $fail('通信失败，请稍后再通知我');
                    }

                }
            }
            return $fail('通信失败，请稍后再通知我');
        });
        $response->send();
    }
    /**
     * 积分支付
     */
    public function PayIntegral(){
        $response = $this->app->handlePaidNotify(function ($message, $fail) {
            if ($message['result_code'] === 'SUCCESS') {
                Db::startTrans();
                $order=IntegralOrderModel::lock(true)->where([ 'order_no'=>$message['out_trade_no'],'state'=>0])->find();
                if($order){

                    try {
                        $order->state=1;
                        $order->save();
                        MemberModel::where(['id'=>$order['member_id']])->setInc('integral',$order['integral']);
                        $member=MemberModel::get($order['member_id']);
                        $data['store_id']=$order['store_id'];
                        $data['member_id']=$order['member_id'];
                        $data['integral']=$order['integral'];
                        $data['residual_integral']=$member['integral'];
                        $data['amount']=$order['amount'];
                        $data['create_time']=time();
                        $data['type']=1;
                        $data['mode']=3;
                        IntegralRecordModel::create($data);
                        // 提交事务
                        Db::commit();
                        return true;
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                        exception($e->getMessage());
                    }
                }
                Db::commit();
            }
            return $fail('通信失败，请稍后再通知我');
        });
        $response->send();

    }

    //用户领取优惠券支付
    public function userReceiveCoupon(){
        $response = $this->app->handlePaidNotify(function ($message, $fail) {
            if ($message['result_code'] === 'SUCCESS') {
                Db::startTrans();
                $order=UserCouponOrderModel::lock(true)->where([ 'order_no'=>$message['out_trade_no'],'state'=>0])->find();
                if($order){
                    try {
                        $order->state=1;
                        $order->save();
                        $data['coupon_id'] = $order->coupon_id;
                        $data['user_id'] = $order->member_id;
                        $data['state'] = 1;
                        $data['download_coupon_id'] =  $order->download_coupon_id;
                        UserCouponModel::create($data);
                        // 提交事务
                        Db::commit();
                        return true;
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                        exception($e->getMessage());
                    }
                }
                Db::commit();
            }
            return $fail('通信失败，请稍后再通知我');
        });
        $response->send();
    }
}