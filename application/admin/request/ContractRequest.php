<?php
namespace app\admin\request;

use app\admin\validates\ContractValidate;

class ContractRequest extends FormRequest
{
    public function validate()
    {
        return (new ContractValidate())->getErrors($this->post());
    }
}