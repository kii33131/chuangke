<?php

namespace app\model;

class SubjectModel extends BaseModel
{
    protected $name = 'subject';

    public function getSubjectList($limit=10){
        return self::where(['is_delete'=>0])->paginate($limit, false, ['query' => request()->param()]);
    }
}