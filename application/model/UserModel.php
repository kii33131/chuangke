<?php

namespace app\model;

use think\permissions\traits\hasRoles;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
class UserModel extends BaseModel
{
	use hasRoles;

    protected $name = 'users';

	/**
	 * Users List
	 *
	 * @time at 2018年11月14日
	 * @param $params
	 * @return \think\Paginator
	 */
    public function getList($params, $limit = self::LIMIT)
    {
    	if (!count($params)) {
    		return $this->paginate($limit);
	    }


    	if (isset($params['name'])) {
		    $user = $this->whereLike('name', '%'.$params['name'].'%');
	    }
	    if (isset($params['email'])) {
    		$user = $this->whereLike('email', '%'.$params['email'].'%');
	    }

	    return $user->paginate($limit, false, ['query' => request()->param()]);
    }


    public function sendMsg($code='123',$type=1,$mobile){

        AlibabaCloud::accessKeyClient('LTAIOEdNclg737S4', 'RcPukTGVEq3Z1yTpcUyfqnQbnMJHt6')
            ->regionId('cn-hangzhou') // replace regionId as you need
            ->asDefaultClient();
        try {
            AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $mobile,
                        'SignName' => "jz",
                        'TemplateCode' => "SMS_170180140",
                        'TemplateParam' => \GuzzleHttp\json_encode(['code'=>$code]),
                        'SmsUpExtendCode' => $code,
                    ],
                ])
                ->request();

            MemberSmsModel::create([
                'code'=>$code,
                'status'=>0,
                'type'=>$type,
                'mobile'=>$mobile,
                'created_at'=>date('Y-m-d H:i:s'),
                'expire_at'=>date('Y-m-d H:i:s',time()+5*60)
            ]);
            success(['code'=>$code ]);
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }

    }

}