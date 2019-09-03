<?php

namespace app\model;

class EsignModel extends BaseModel
{
    protected $name = 'esign';

	/**
	 * Users List
	 *
	 * @time at 2018年11月14日
	 * @param $params
	 * @return \think\Paginator
	 */
    public function getList($params, $limit = self::LIMIT)
    {

        if (!count($params)) {
    		return $this->order('is_default desc,created_at desc')->paginate($limit);
	    }

    	if (isset($params['accounts_name'])) {
            $esign = $this->whereLike('accounts_name', '%'.$params['accounts_name'].'%');
	    }
	    if (isset($params['email'])) {
            $esign = $this->whereLike('org_idnumber', '%'.$params['org_idnumber'].'%');
	    }

	    return $esign->order('is_default desc,created_at desc')->paginate($limit, false, ['query' => request()->param()]);
    }

    static public function getEsignByAccount($account_id){
        return self::where(['account_id'=>$account_id,'is_default'=>1])->find();
    }

}