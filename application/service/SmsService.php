<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 18:06
 */
namespace app\service;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
class SmsService
{

    public function sendMsg(){

        AlibabaCloud::accessKeyClient('LTAIOEdNclg737S4', 'RcPukTGVEq3Z1yTpcUyfqnQbnMJHt6')
            ->regionId('cn-hangzhou') // replace regionId as you need
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => "18579058297",
                        'SignName' => "jz",
                        'TemplateCode' => "SMS_170180140",
                        'TemplateParam' => \GuzzleHttp\json_encode(['code'=>'123456']),
                        'SmsUpExtendCode' => "123456",
                    ],
                ])
                ->request();
            //print_r($result->toArray());

            success([]);
        } catch (ClientException $e) {
            //echo $e->getErrorMessage() . PHP_EOL;
            error($e->getErrorMessage(),999);
        } catch (ServerException $e) {
            error($e->getErrorMessage(),999);
        }
    }


}
