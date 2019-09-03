<?php
namespace app\admin\request;

use app\admin\validates\ChannelValidate;

class ChannelRequest extends FormRequest
{
    public function validate()
    {
        return (new ChannelValidate())->getErrors($this->post());
    }
}