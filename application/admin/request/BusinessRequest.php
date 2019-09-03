<?php
namespace app\admin\request;

use app\admin\validates\BusinessValidate;

class BusinessRequest extends FormRequest
{
    public function validate()
    {
        return (new BusinessValidate())->getErrors($this->post());
    }
}