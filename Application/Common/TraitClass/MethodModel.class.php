<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.shopsn.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.shopsn.net）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\TraitClass;

use Common\Tool\Tool;

/**
 * 数据库操作方法 
 */
trait MethodModel
{    
    protected $isString    = true;
    
    protected $searchDbKey ;  //获取搜索key
    
    protected $setComment  = [];
   
    protected  $selectColums = [];
    
    protected $keyArray = array();
    
    /**
     * @param field_type $searchKey
     */
    public function setSearchDbKey($searchKey)
    {
        $this->searchDbKey = $searchKey;
    }

    /**
     * @return the $keyArray
     */
    public function getKeyArray()
    {
        return $this->keyArray;
    }

    /**
     * @param multitype: $keyArray
     */
    public function setKeyArray($keyArray)
    {
        $this->keyArray = $keyArray;
    }


    /**
     * @return the $selectColums
     */
    public function getSelectColums()
    {
        return $this->selectColums;
    }

    /**
     * @param []: $selectColums
     */
    public function setSelectColums($selectColums)
    {
        $this->selectColums = $selectColums;
    }

    /**
     * 获取规格项名称
     * @param array $data 商品数组
     * @param string $splitKey 分割建
     * @return array
     */
    public function getSpecItemName(array $data, $splitKey, $whereField= null)
    {
        if (empty($data) || !is_array($data) || !is_string($splitKey) || empty($splitKey)) {
            return array();
        }
    
        $idString = Tool::characterJoin($data, $splitKey);
        if (false !== strpos($idString, '_')) {
            $single = explode(',', str_replace('"', null, $idString));
            
            
            foreach ($single as $key => & $value) {
               $value = str_replace('_', ',', $value);
            }
            $idString = implode(',', $single);
        }
       
        if (empty($idString)) {
            return  array();
        }
        
        $whereField = empty($whereField) ? static::$id_d : $whereField;
        
        $specData = $this->where($whereField.' in ('.$idString.')')->select();
        return empty($specData) ? array() : $specData;
    }
    
    /**
     * 减少库存
     */
    public function delStock (array $goods)
    {
        if (!$this->isEmpty($goods)) {
            $this->rollback();
            return false;
        }
    
        //批量更新
        $pasrseData = array();
        foreach ($goods as $key => $value)
        {
            if (empty($value['goods_num'])) {
                $this->rollback();
                return false;
            }
            $pasrseData[$value['goods_id']][] = static::$stock_d.'-'. $value['goods_num'];
        }
        
        $keyArray = $this->keyArray;
        if (empty($keyArray)) {
            $this->error = '参数错误';
            $this->rollback();
            return false;
        }
    
        /** UPDATE db_goods  SET `id`= CASE `id` WHEN 1093 THEN "1093"
         END,`stock`= CASE `id` WHEN 1093 THEN "stock-1"
         END WHERE `id` in(1093);
         */
      
        $this->isString = false; //批量更新 不需要 添加引号
        $sql = $this->buildUpdateSql($pasrseData, $keyArray, $this->getTableName());
        try {
            $status = parent::execute($sql);
        } catch (\Exception $e) {
            $this->error = '库存不足';
            $this->rollback();
            return false;
        }
        return $status;
    }
    
    
    /**
     * 获取表详细数据
     */
    public function getNotes()
    {
        $key = md5('Table_NAME_FIELD_'.$this->getTableName());
        $data = S($key);
        
        if (empty($data)) {
            $data = $this->noCacheTableData();
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        S($key, $data, 86400);
        
        return $data;
        
    }
    
    /**
     * 无缓存 表数据
     */
    public function noCacheTableData ()
    {
        $data = $this->query('show  FULL columns from '.$this->getTableName());
        return $data;
    }
    
    /**
     * 获取表注释 
     */
    public function getComment (array $hidden = array())
    {   
      
        $validate = $this->getTableComment();
       
        if (empty($hidden)) {
            return $validate;
        }
      
        $temp = [];
         
        foreach ($validate   as $key => $value)
        {
            if (!in_array($key, $hidden)) {
                $temp[$key] = $value;
            }
           
        }
        
        return $temp;
    }
    
    /**
     * 获取要查询的注释
     */
    public function getShowComment(array $show = [])
    {
         $validate = $this->getTableComment(); 
         
         if (empty($show)) {
             return $validate;
         }
         
         $temp = [];
         
        foreach ($validate   as $key => $value)
        {
            if (in_array($key, $show)) {
                $temp[$key] = $value;
            }
        }
        return $temp;
    }
    
    /**
     * 获取表注释
     */
    private function getTableComment()
    {
        $notes = $this->getNotes();
        
        $validate = array();
        
        $start = 0;
        foreach ($notes as $key => & $value) {
            if (empty($value['comment'])) {
                throw new \Exception('请在'.$this->getTableName().'表中添加注释');
            }
        
            $validate[$value['field']] = $value['comment'];
             
            if (false === ($start = mb_strpos($validate[$value['field']], '【'))) {
                continue;
            }
            $validate[$value['field']] = mb_substr($validate[$value['field']], 0, $start);
        }
        
        return $validate;
    }
    
    /**
     * 设置 要查询的 注释 
     */
    public function setComment(array $fields)
    {
        if (empty($fields) || !is_array($fields)) {
            return array();
        }
        
        $data = $this->getNotes();
        
        if (empty($data)) {
            throw new \Exception('系统在崩溃的边缘');
        }
        
        foreach ($data as $key => $value) {
            if (in_array($value['field'], $fields, true)) {
                continue;
            }
            unset($data[$key]);
        }
        $this->setComment = $data;
        
        return $data;
    }
    
    
    
    /**
     * 获取显示列表标题
     */
    public function getListTitle ($field, $cacheKey = 'ORDER_RETURN_TITLE_CACHE', $hidden = null)
    {
        $orderReturnTitle = S($cacheKey);
    
         if (empty($orderReturnTitle)) {
            $listTitle = $this->setComment($field);
    
            $orderReturnTitle = $this->getComment($hidden);
    
            S($cacheKey, $orderReturnTitle, 86400);
        }
        return $orderReturnTitle;
    }
    
    /**
     * 获取数据
     */
    public function getUserNameById($id, $field)
    {
        $whereId = $this->searchDbKey ? $this->searchDbKey : static::$id_d;
        
        $thisField = $this->getDbFields();
        if (!in_array($field, $thisField, true) || !in_array($whereId, $thisField, true)) {
            return null;
        }
    
        if (($id = intval($id)) === 0 ) {
            return null;
        }
        $name = $this->where($whereId.'=%d', $id)->getField($field);
        return $name;
    }
    
    /**
     * 根据订单信息 获取单挑数据
     */
    public function getData ($id, array $field)
    {
        if (($id = intval($id)) === 0 || empty($field)) {
            return array();
        }
         
        $data = $this->field($field, true)->where(static::$id_d.'=%d', $id)->find();
         
        if (empty($data)) {
            return array();
        }
        return empty($data[$this->selectColumName]) ? $data : $data[$this->selectColumName];
    }
    
    protected  $selectColumName;
    /**
     * @return the $selectColumName
     */
    public function getSelectColumName()
    {
        return $this->selectColumName;
    }
    
    /**
     * @param field_type $selectColumName
     */
    public function setSelectColumName($selectColumName)
    {
        $this->selectColumName = $selectColumName;
    }
}