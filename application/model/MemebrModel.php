<?php

namespace app\model;

use think\Db;

class MemebrModel extends BaseModel
{
    protected $name = 'member';

    //检查验证码
    public function checkCode($codes,$type){
       $code =Db::table('tbl_member_sms');
       $code = $code->where([
           'code'=>$codes,
           'status'=>0,
           'type'=>$type
       ]);
       $code = $code->where('expire_at','>',date('Y-m-d H:i:s'));
       $sms= $code->find();
       if($sms){
           $result=Db::table('tbl_member_sms')->where(['id'=>$sms['id']])->update(['status'=>1]);
           if($result){
               return ['code'=>200,'msg'=>'success'];
           }else{
               return ['code'=>30001,'msg'=>'无效的验证码'];
           }
       }else{
           return ['code'=>30001,'msg'=>'无效的验证码'];
       }
    }

    public function examine($data,$id){
        if(isset($data['channelids']) && $data['channelids']){
            $channelids = $data['channelids'];
            if(empty($channelids)){
                error('channelids不能为空','10000');
            }
            unset($data['channelids']);
        }

        //echo $channelids;exit;
        Db::startTrans();
        try {

            Self::where(['id'=>$id])->update($data);
            Db::table('tbl_member_channel')->where([
                'member_id'=>$id
            ])->delete();
            if(isset($channelids) && $channelids){
                foreach ($channelids as $v){
                    Db::table('tbl_member_channel')->insert(['member_id'=>$id,'channel_id'=>$v,'created_at'=>date('Y-m-d H:i:s')]);
                }
            }
            Db::commit();
        } catch (ApiException $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }catch(\Exception $e){
            Db::rollback();
            throw $e;
        }
    }

    public static function getUserinfoByToken($token){
        return Db::table('tbl_member_token')->where(['token'=>$token])->find();
    }
    public function member($id){
        $member=self::where(['id'=>$id])->field('account,mobile,address,name,cart_id,id_img_frontal,id_img_back,created_at,id,is_authentication,hand_held_certificate,province,city,city_id,number as mnumber,rejection')->find();
        if($member){
            $channel=$this->alias('m')
                ->leftJoin('member_channel c','m.id=c.member_id')
                ->leftJoin('channel l','l.id=c.channel_id')->where('c.member_id',$member['id'])->field('l.name,l.code,l.id')->select();
            ;
            $member['channel'] =$channel;
            $bank = Db::table('tbl_member_bank')->where(['member_id'=>$id])->find();
            if($bank){
                $member['number'] = $bank['number'];
                $member['bank_name'] = $bank['bank_name'];
                $member['sub_branch'] = $bank['sub_branch'];
                $member['bank_address'] = $bank['address'];
            }
        }
        return $member;
    }

    public function bankDetail($user_id){
      return  Db::table('tbl_member_bank')->where(['member_id'=>$user_id])->find();
    }

    public function saveUsers($userdata,$bankdata,$id){
        if(!empty($userdata)){
            $this->examine($userdata,$id);
        }
        if(!empty($bankdata)){
          $this->create_bank($bankdata,$id);
        }
    }


    public function create_bank($data,$id){
        //晚点放事物
        $bank= Db::table('tbl_member_bank')->where(['member_id'=>$id])->find();
        if($bank){
            Db::table('tbl_member_bank')->where(['member_id'=>$id])->update($data);
        }else{
            $data['member_id']=$id;
            self::create($data);
        }
    }
    //0123310011199107276000029

    public function memberList($limit,$data){
        $member=$this->alias('m')
        ->leftJoin('member_bank b','b.member_id=m.id')
        ;
        if(isset($data['name'])){
            $member= $member->whereLike('m.name', '%'.$data['name'].'%');
        }
        if(isset($data['mobile'])){
            $member = $member->where('m.mobile' ,$data['mobile'] );
        }

        if(isset($data['cart_id'])){
            $member = $member->where('m.cart_id' ,$data['cart_id'] );
        }
        $member = $member->where('m.type' ,1 );
        $member = $member->where('m.is_authentication' ,'<>',0 );
        $list= $member->field('m.id,m.name,m.mobile,b.number as bnumber ,m.cart_id,b.bank_name,b.address,b.member_id,m.is_authentication,b.sub_branch,b.id as bid,m.number,m.province,m.city')->order('m.is_authentication desc,m.id desc')
            ->paginate($limit, false, ['query' => request()->param()])
            ->each(function ($item){
                $item->channel = Db::table('tbl_member_channel')->leftJoin('tbl_channel','tbl_member_channel.channel_id = tbl_channel.id')->where(['member_id'=>$item->id])->field('name')->select();
            });
        ;

        return $list;
    }

    public function getMemberNumber(){
        $num=self::where(['is_authentication'=>1])->count();
        return $num+1+6000000;
    }


    public function untying($data,$user_id){
        $result=self::where(['mobile'=>$data['mobile']])->find();
        if($result){
            error('改电话号码已经存在帐号','10000');
        }
        self::where(['id'=>$user_id])->update(['account'=>$data['mobile'],'mobile'=>$data['mobile']]);
    }

}