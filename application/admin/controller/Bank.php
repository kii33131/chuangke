<?php

namespace app\admin\controller;

use app\admin\request\BankRequest;
use app\model\BankModel;

class Bank extends Base
{
    public function view(BankModel $bankModel,BankRequest $request){

        if ($this->request->isPost()) {
            $bankModel->updateBy(1, $request->post()) !== false ? $this->success('编辑成功', url('Bank/view')) : $this->error('编辑失败');
        }
        $this->bank=$bankModel->findBy(1);
        return $this->fetch();
    }
}