<?php


namespace app\api\controller;


use app\exceptions\ApiException;
use think\Exception;

class Upload extends Base
{

    /**
     * 图片上传单张
     * @url api/upload/img
     * @http POST
     */
    public function img(){
        if(!empty($_FILES['file']['name'])){
            $file = request()->file('file');
            $info = $file->move(config('upload_file'));
            if($info){
                success($info->getSaveName());
            }else{
                throw new Exception([
                    'msg' => $file->getError(),
                    'errorCode' => '999'
                ]);
            }
        }else{
            throw new ApiException([
                'msg' => '未上传图片',
                'errorCode' => '10000'
            ]);
        }
    }

    public function imgs(){
        $data =[];
        if(!empty($_FILES['file']) ){
            $files = request()->file('file');
            foreach($files as $file){
                // 移动到框架应用根目录/uploads/ 目录下
                $info = $file->move( config('upload_file'));
                if($info){
                    $data[] = $info->getSaveName();
                }else{
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }

        }else{
            throw new ApiException([
                'msg' => '未上传图片',
                'errorCode' => '10000'
            ]);
        }

        success($data);
    }




    public function imgstest(){

      //  var_dump($_FILES);exit;
        if(!empty($_FILES) ){
            $pictures = [
                'file',
                'file2'
            ];
            foreach ($pictures as $val){
                if(!empty($_FILES[$val]['name'])){
                    //$file = request()->file($val);

                   // $info = $file->move(config('upload_file'));
                    // Open the file to get existing content
                    $current = file_get_contents($_FILES[$val]['tmp_name']);
                    file_put_contents('aabbbkk.jpg',$current);
                    //move_uploaded_file($_FILES[$val]['tmp_name'], 'aabbb.jpg');;
                    //var_dump($_FILES[$val]['tmp_name']);exit;

                }
            }

            exit;
        }
    }



}