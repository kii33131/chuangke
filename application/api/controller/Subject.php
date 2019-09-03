<?php


namespace app\api\controller;


use app\model\SubjectModel;

class Subject extends Base
{

    public function index(){
        $model = new SubjectModel();
        $data=$model->getSubjectList($limit=1000);
        success($data);
    }

}