<?php


namespace app\api\controller;




use think\Db;

class Channel extends Base
{
   public function index(){

      $channel= Db::table('tbl_channel')->where(['is_delete'=>0])->paginate($this->listRows, false, ['query' => request()->param()]);
      success($channel);
   }
}