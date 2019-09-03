<?php

namespace app\model;

class TemplateModel extends BaseModel
{
    protected $name = 'template';

    public function getTemplateList($limit){

        $template=$this->alias('t')->join('users u','u.id=t.user_id')
                 ->field('t.id,u.name user_name,t.name,t.content,t.create_time')
                 ->paginate($limit);
        return $template;
    }


}