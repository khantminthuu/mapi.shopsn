<?php
namespace Common\Model;

class SystemConfigModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//id

	public static $key_d;	//子配置键名

	public static $configValue_d;	//配置值

	public static $classId_d;	//所属父级分类编号

	public static $parentKey_d;	//父级key

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $currentId_d;	//当前配置分类【编号】

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    
    public function getAllConfig(array $option = null)
    {
        $data = $this->field('create_time,update_time', true)->where($option)->select();
        
        if (!empty($data))
        {
            foreach ($data as $key => &$value)
            {
                if (!empty($value['config_value']))
                {
                    $unData = unserialize($value['config_value']);
                    unset($data[$key]['config_value']);
                    $value = array_merge($value, $unData);
                }
            }
        }
        return $data;
    }
    
    /**
     * @desc 依据某个键 获取 子集
     * @param string $key  父级键名
     * @return array
     */
    public function getDataByKey($key)
    {
        if (empty($key)) {
            return array();
        }
    
        $data = $this->where(self::$parentKey_d .' = "%s"', $key)->getField(self::$classId_d.','.self::$configValue_d);
      
        if (empty($data)) {
            return array();
        }
        
        foreach ($data as $key => & $value)
        {
            $value = unserialize($value);
        }
    
        return $data;
    }
}