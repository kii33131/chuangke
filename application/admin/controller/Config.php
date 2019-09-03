<?php
namespace app\admin\controller;

use app\admin\request\ConfigRequest;
use app\model\ConfigModel;

class Config extends Base
{

   public function taxrate(ConfigModel $configModel,ConfigRequest $request){
       if ($this->request->isPost()) {
           $configModel->updateBy(1, $request->post()) !== false ? $this->success('编辑成功', url('Config/taxrate')) : $this->error('编辑失败');
       }
       $this->config=$configModel->findBy(1);
       return $this->fetch();
   }

}