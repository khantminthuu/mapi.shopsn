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
			'id' => ['number'=>'必须传入店铺ID']
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
			$data = ['hide'=>0];
			$res = $this->modelObj->where($where)->save($data);
		
		if($res){
			echo "success";	
		}else{
			echo "unsuccess";
		}
		// }else($save==0){
		// 	$save = 1;
		// 	$this->modelObj->save($save);
		// }


	}

}