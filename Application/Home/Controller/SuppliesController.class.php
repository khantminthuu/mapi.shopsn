<?php
/**
 * Created by PhpStorm.
 * User: Yisu-Administrator
 * Date: 2017/5/22
 * Time: 13:32
 */

namespace Home\Controller;
/**
 * Class SuppliesController
 * @package Home\Controller
 * 耗材租赁机控制器
 */
class SuppliesController extends CommonController
{
	/**
	 *补充耗材记录
	 */
	public function supply_record()
	{
		$userId = I('app_user_id');
		$count = M('supplementary_supplies')
			->field(
				'add_time,consumables,num,status'
			)
			->where(
				['user_id' => $userId]
			)
			->count();
		$page = new \Think\Page($count, 15);
		$show = $page->show();
		$detail = M('supplementary_supplies')
			->field(
				'add_time,consumables,num,status'
			)
			->where(['user_id' => $userId])
			->limit($page->firstRow, $page->listRows)
			->select();
		if ($detail)
			$this->ajaxReturnData(1, '操作成功', $detail);
		else
			$this->ajaxReturnData(0, '返回失败', '暂无数据');
		
	}
	
	/**
	 * 打印租赁机-抄表记录
	 */
	public function copy_table()
	{
		$printer_id = I('printer_id');
		$copy_table = M('printer_meter')
			->where([
				'printer_id' => $printer_id,
			])
			->field(
				'meter_time,meter_reading,colour_num,black_num,pay_price,pay_status'
			)
			->select();
		if ($copy_table)
			$this->ajaxReturnData(1, '操作成功', $copy_table);
		else
			$this->ajaxReturnData(0, '返回失败', '');
	}
	
	/**
	 * 打印机租赁
	 */
	public function lease()
	{
		$userId = I('app_user_id');
		$count = M('printer_rental')
			->field('id')
			->where([
				'user_id' => $userId,
			])
			->select();
		$page = new \Think\Page($count, 5);
		$field = 'start_time,due_time,goods_id,addtime,status,deposit,pay_type';
		$printer_detail = M('printer_rental')
			->field($field)
			->where([
				'user_id' => $userId,
			])
			->limit($page->firstRow, $page->listRows)
			->select();
		if ($printer_detail)
			$this->ajaxReturnData(1, '操作成功', $printer_detail);
		else
			$this->ajaxReturnData(0, '返回失败', "");
	}
	
	/**
	 * 补充耗材需求
	 */
	public function supply_need()
	{
		if (IS_POST) {
			$data['user_id'] = I('app_user_id');
			$data['printer_id'] = I('printer_id');//租赁打印机表id
			$data['add_time'] = time();
			$data['consumables'] = I('consumables');
			$data['num'] = I('num');
			$data['remark'] = I('remark');
			$re = M('supplementary_supplies')->add($data);
			if ($re)
				$this->ajaxReturnData(1, '提交成功', '');
			else
				$this->ajaxReturnData(0, '提交失败', '');
		}
		
	}
	
	/**
	 * 租赁合同详情
	 */
	public function supply_detail()
	{
		$goods_id = I('goods_id');
		$supply_detail = M('printer_rental')
			->where(
				['goods_id' => $goods_id]
			)
			->field(
				'id,title,start_time,due_time,lease_price,black_price,colour_price,deposit,status'
			)
			->find();
		$copy_table = M('printer_meter')
			->where(
				['printer_id' => $supply_detail['id']]
			)
			->field(
				'meter_time,meter_reading,colour_num,black_num,pay_price,pay_status'
			)
			->select();
		$data = array(
			'supply_detail' => $supply_detail,
			'copy_table'    => $copy_table,
		);
		$this->ajaxReturnData(1, '操作成功', $data);
	}
	
}