<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/12 0012
 * Time: 下午 16:31
 */
namespace app\admin\validates;;

use think\Validate;

abstract class AbstractValidate extends Validate
{

	/**
	 * Get Validate Errors
	 *
	 * @time at 2018年11月12日
	 * @param $data
	 * @return array
	 */
	public function getErrors($data)
	{
		$this->check($data);

		return $this->getError();
	}


	public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->rule[$name] = $value;
    }

    /**
     * 验证是否为正数
     */
    protected function positiveNumber($value,$rule,$data=[],$name,$description){
        if(is_numeric($value) && $value >= 0){
            if($rule == '1' && $value <= 0){
                return $description . '必须为正数';
            }
            return true;
        }
        return $description . '必须为正数';
    }

    /**
     * 验证是否为百分比（可两位小数）
     */
    protected function percentage($value,$rule,$data=[],$name,$description){
        if(preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $value) &&  $value <=100 ){
            return true;
        }else{
            return $description . '输入不正确';
        }
    }


}