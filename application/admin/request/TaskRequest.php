<?php
namespace app\admin\request;

use app\admin\validates\TaskValidate;

class TaskRequest extends FormRequest
{
    public function validate()
    {
        return (new TaskValidate())->getErrors($this->post());
    }
}