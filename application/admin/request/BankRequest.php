<?php
namespace app\admin\request;

use app\admin\validates\BankValidate;

class BankRequest extends FormRequest
{
    public function validate()
    {
        return (new BankValidate())->getErrors($this->post());
    }
}