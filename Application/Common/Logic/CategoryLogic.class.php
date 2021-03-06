<?php
namespace Common\Logic;
use Think\Cache;
use Common\Model\CategoryModel;

class CategoryLogic extends AbstractGetDataLogic
{
	public function __construct(array $data=[], $split ='')
	{
		$this->data = $data;

		$this->splitKey = $split;

		$this->modelObj = new CategoryModel();
	}
	## 	abstract method
	public function getModelClassName() :string
	{
		return CategoryModel::class;
	}

	public function getResult() :array
	{

	}

	public function getValidateByLogin()
	{
		$message = [
			'id' => ['number'=>'We need Id Number get method']
		];
		return $message;
	}
	##	abstract end

	public function getAllCategory()
	{
		$where['type'] = 0;
		$whereTwo['type'] = 1;
		$field = 'detail,hide';
		$getAllCategory = $this->modelObj->where($where)->field($field)->select();
		$getRecomment = $this->modelObj->where($whereTwo)->field($field)->select();
		$arr = [
			'AllCategory' => $getAllCategory,
			'Recomment' => $getRecomment
		];
		return $arr;
	}
	public function saveShow()
	{
		$where['id'] = $this->data['id'];
		$save = $this->modelObj->where($where)->field('hide')->select();
		$save = $save[0];
		if($save['hide']==1){
			$data = ['hide'=>0];
			$res = $this->modelObj->where($where)->save($data);
		}else{
			$data = ['hide'=>1];
			$res = $this->modelObj->where($where)->save($data);
		}		
		return true;
	


	}

}