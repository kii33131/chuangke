<?php
namespace app\admin\controller;

use app\admin\request\ConfigRequest;
use app\model\ConfigModel;

class WechatConfig extends Base
{
    public function index(ConfigModel $config,ConfigRequest $request)
    {
        $configInfo = $config->findBy(1);
        if ($request->isPost()) {
            $data = $request->post();
            unset($data['logo']);
            //logo上传
            if(!empty($_FILES['logo']['name'])){
                $file = request()->file('logo');
                $info = $file->move(config('upload_file'));
                if($info){
                    $data['logo'] = $info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }
            if($config->updateBy(1, $data) !== false){
                //删除旧LOGO图片
                if(!empty($configInfo['logo']) && !empty($data['logo'])){
                    @unlink(config('upload_file') . $configInfo['logo']);
                }
                $this->success('保存成功', url('WechatConfig/index'));
            }else{
                $this->error('');
            }
        }
        $this->config = $configInfo;
        return $this->fetch();
    }
}