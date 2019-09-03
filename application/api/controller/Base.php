<?php


namespace app\api\controller;

use app\model\AttorneyModel;
use app\model\ConfigModel;
use app\model\MemberModel;
use think\App;
use think\Controller;
use think\Db;
use think\Request;

class Base extends Controller
{
    protected $uid;//用户id
    protected $listRows = 10;//每页显示数量
//    protected $url; //请求url
//    protected $parameters;//请求参数
    public $userinfo = [];


    public function __construct(App $app = null)
    {

        parent::__construct($app);
        $this->setListRows();
        //验证权限
        $action=\think\facade\Request::action();
        $controller=\think\facade\Request::controller();
        $rule = $controller.'/'.$action;
        if(!in_array($rule,['User/sendmsg','User/login','Home/index','Task/details','Channel/index','Upload/img','Upload/imgs','Upload/imgstest','User/redirects','User/redirecty','User/contentlist','Achievement/achievementlist2','User/info2'])){
            $result=$this->check_auth();
            if($result['code']==200){
                $this->userinfo = $result['data'];
            }else{

                error($result['code'],$result['data']);
            }
        }
        //设置分页数量
//        $this->url= request()->url(true);
//        $this->parameters = \GuzzleHttp\json_encode(input('post.'));
//        $Request = new Request();
//        $this->method=$Request->method();
    }

    public function check_auth() {
        $token=\think\facade\Request::instance()->header('AUTHORIZATION');
        $token = str_replace('Bearer ', '', $token);
        if(!$token){
            return Array(
                "code" => 10009,
                "data" => '缺少token'
            );
        }
        $tokendetail=Db::table('tbl_member_token')->where([
            'token'=>$token
        ])->find();
        if ($tokendetail) {
            //获取用户信息
            $memberdetail=Db::table('tbl_member')->where([
                'id'=>$tokendetail['member_id']
            ])->field('mobile,type,is_authentication,id')->find();
            $business=Db::table('tbl_business')->where([
                'member_id'=>$memberdetail['id']
            ])->find();
            if($business){
                $memberdetail['business_id'] = $business['id'];
            }else{
                $memberdetail['business_id'] =0;
            }
            $data['userinfo'] =$memberdetail;
            $data['token']=$token;
            return Array(
                "code" => 200,
                "data" => $data
            );
        } else {
            return Array(
                "code" => 10001,
                "data" => 'Invalid Token'
            );
        }
    }

    /**
     * 设置分页数量
     */
    protected function setListRows(){
        if(preg_match("/^[1-9][0-9]*$/",input('list_rows'))){
            $this->listRows = input('list_rows');
        }
    }
    protected function checkParams(&$params)
    {

        foreach ($params as $key => $param) {
            if (!$param || $key == 'limit' || $key == 'page') {
                unset($params[$key]);
            }
        }
    }


    protected function getCode($len=6){

        $chars = str_repeat('0123456789', 3);
        // 位数过长重复字符串一定次数
        $chars = str_repeat($chars, $len);
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;
    }


    public function  create_guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = substr($charid, 0, 8) . '-' . substr($charid, 8, 4) . '-'
            . substr($charid, 12, 4) . '-' . substr($charid, 16, 4) . '-'
            . substr($charid, 20, 12);
        return $uuid;

    }

    public  function doLogin($mobile,$type,$uuid,$business_id=0){

        Db::startTrans();
        try {
            $member=Db::table('tbl_member')->lock(true)->where([
                    'mobile'=>$mobile
                ])->find();
            if(!$member){
                $member_id=Db::table('tbl_member')->insertGetId([
                    'account'=>$mobile,
                    'mobile'=>$mobile,
                    'type'=>$type,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
                // 如果是企业端登录 初始化企业
                if($type==2){
                    $serverdata['business_id']= Db::table('tbl_business')->insertGetId([
                        'member_id'=>$member_id,
                        'created_at'=>date('Y-m-d H:i:s')
                    ]);
                    $serverdata['rate']=0;//默认技术服务费率
                    db::table('tbl_service_charge')->insertGetId($serverdata);
                }
                //企业邀请创客注册添加关系
                if($business_id && $type==1){
                    $business_member= Db::table('tbl_business_member')->where(['member_id'=>$member_id,'is_delete'=>0])->find();
                    if(!$business_member){
                        Db::table('tbl_business_member')->insert(['member_id'=>$member_id,'business_id'=>$business_id,'created_at'=>date('Y-m-d H:i:s')]);
                    }
                }

            }else{
                if($member['type'] !=$type){
                    if($member['type']==1){
                        Db::rollback();
                        error('您是创客帐号请登录创客端',10000);
                    }else{
                        Db::rollback();
                        error('您是企业帐号请企业端登录',10000);
                    }
                }
                $member_id =$member['id'];
            }

            //生成token
            $token=Db::table('tbl_member_token')->where([
                'member_id'=>$member_id
            ])->find();
            if(!$token){
                Db::table('tbl_member_token')->insert([
                    'member_id'=>$member_id,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'token'=>$uuid
                ]);

            }else{
                Db::table('tbl_member_token')->where([
                    'id'=>$token['id']
                ])->update(['token'=>$uuid,'update_at'=>date('Y-m-d H:i:s')]);
            }
            $memberdetail=Db::table('tbl_member')->where([
                'mobile'=>$mobile
            ])->field('mobile,type,is_authentication,id')->find();
            $business=Db::table('tbl_business')->where([
                'member_id'=>$memberdetail['id'],
                'is_delete'=>0
            ])->find();
            $this->userinfo['token']=$uuid;

            if($business){
                $memberdetail['business_id'] = $business['id'];
            }else{
                $memberdetail['business_id'] =0;
            }
            $this->userinfo['userinfo'] =$memberdetail;
            Db::commit();
            return $this->userinfo;

            //return $result;
        } catch (ApiException $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }catch(\Exception $e){
            Db::rollback();
            throw $e;
        }

    }
    /*public function __destruct()
    {
        // TODO: Implement __destruct() method.

        $data['member_id']=$this->uid;
        $data['url']=$this->url;
        $data['parameters']=$this->parameters;
        $data['create_time']=date('Y-m-d H:i:s');
        $data['method']=$this->method;
        MemberRequestLogModel::create($data);

    }*/
}