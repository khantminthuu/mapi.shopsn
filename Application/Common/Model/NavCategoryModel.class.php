<?php
	namespace Common\Model;

	class NavCategoryModel extends BaseModel
	{

	public static $id_d;	//

	public static $title_d;	//

	public static $picUrl_d;	//

	public static $hideStatus_d;	//

	public static $detail_d;	//

		private static $obj;
		public static function getInitnation(){
			$class = __CLASS__;
			return self::$obj = !(self::$obj instanceof $class) ? new self():self::$obj;
		} 
		public function getData(){
			$data = $this->where(['hide_status'=>1 ])->field('id,title')->select();
			return $data;
		}
	}