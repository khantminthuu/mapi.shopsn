<?php
namespace Common\Model;

use Common\Tool\Tool;
use Common\TraitClass\ModelToolTrait;

/**
 * 地址模型 
 */
class RegionModel extends BaseModel
{
    use ModelToolTrait;
    private static $obj;

	public static $id_d;

	public static $parentid_d;
	
	public static $name_d;

	public static $type_d;

	public static $displayorder_d;

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    
    /**
     * 获取数据 并添加标识 
     */
    public function getContent($id)
    {
        if (!is_numeric($id)) {
            return array();
        }
        
        // 此处 可显示出 与数据库字段 的无关性【
        
        $data      = $this->getAttribute(array(
            'field' => array(self::$displayorder_d),
            'where' => array(
               self::$parentid_d => $_POST['id']
            )
        ), true);
        
        if (empty($data)) {
            return array();
        }
        foreach ($data as $key => & $value) {
            if (empty($value[self::$name_d])) {
                continue;
            }
            $value[self::$name_d] = Tool::getFirstEnglish($value[self::$name_d]).' '. $value[self::$name_d];
        }
        return $data;
    }
    /**
     * 获取地址 
     */
    public function getArea (array $data, $split)
    {
        if (!$this->isEmpty($data) || !is_string($split)) {
            return array();
        }
        
        $idString = Tool::characterJoin($data, $split);
        
        if (empty($idString)) {
            return $data;
        }
        $area = $this->field(self::$id_d.','.self::$name_d.','.self::$parentid_d)->where(self::$id_d .' in('.$idString.')')->select();
        if (empty($area)) {
            return $data;
        }
        $area = self::getLevelTop($area, self::$parentid_d);
        
        // 纠正 编号
        foreach ($area as $key => &$value) {
            foreach ($data as $name => & $stock) {
                if ($key === $name) {
                    $value[self::$id_d] = $stock[$split];
                }
                   
            }
        }
        
        foreach ($area as $id => $address) {
            foreach ($data as $name => & $stock) {
                if ($address[self::$id_d] === $stock[$split]){
                    $stock[self::$name_d] = $address[self::$name_d];
                }
                
            }
        }
        return $data;
    }
    
    /**
     * 获取 上级地区
     */
    private  function getLevelTop ($area, $split)
    {
        static $level;
        $idString = Tool::characterJoin($area, $split);
        
        if (empty($idString)) {
            return $area;
        }
        
        $data = $this->field(self::$id_d.','.self::$name_d.','.self::$parentid_d)->where(self::$id_d .' in('.$idString.')')->select();
       
        if (empty($data)) {
            return $area;
        }
        $flag ='';
        foreach  ($data as $key => $value) {
            
            foreach ($area as $address =>  & $detail) {
                if ($value[self::$id_d] !== $detail[self::$parentid_d]) {
                   continue;
                }
                //保证下一次循环 值还在
                $data[$key][self::$name_d] .='-'. $detail[self::$name_d];
                
                $flag .= $value[self::$name_d].'-'.$detail[self::$name_d].'-';
                
                $detail[self::$name_d] = substr($flag, 0, -1);
                
                $level[$address] =$detail; 
                
                $flag = null;
            }
        }
        foreach ($data as $key => $value) {
            if ($value[self::$parentid_d] == 0 || empty($value)) {
                return $level;
            } else {
               return $this->getLevelTop($data, $split);
            }
        }
        return $level;
    }
    /**
     * 得到收货地址的省市区
     *
     */
    public function getcityInfo( $prov, $city, $dist){
        $data['prov_name'] = $this->where('id = '.$prov)->field('name')->find()['name'];
        $data['city_name'] = $this->where('id = '.$city)->field('name')->find()['name'];
        $data['dist_name'] = $this->where('id = '.$dist)->field('name')->find()['name'];
        return $data;
    }
    /**
     * 得到收货地址的省市区
     * @author王波
     */
    public function getRegionByAddress($address){
        foreach ($address as $key => $value) {
            $address[$key]['prov'] = $this->where(['id'=>$value['prov']])->getField('name
                ');
            $address[$key]['city'] = $this->where(['id'=>$value['city']])->getField('name
                ');
            $address[$key]['dist'] = $this->where(['id'=>$value['dist']])->getField('name
                ');
        }
        
        return $address;
    }
    /**
     * 得到收货地址的省市区(单条)
     * @author王波
     */
    public function getRegionByOne($address){       
        $address['prov_name'] = $this->where(['id'=>$address['prov']])->getField('name');
        $address['city_name'] = $this->where(['id'=>$address['city']])->getField('name');
        $address['dist_name'] = $this->where(['id'=>$address['dist']])->getField('name');
        return $address;
    }
}